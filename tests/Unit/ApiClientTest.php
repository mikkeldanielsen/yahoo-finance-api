<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\Context\ContextManagerInterface;
use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\IndustryResult;
use Scheb\YahooFinanceApi\Results\ScreenerResult;
use Scheb\YahooFinanceApi\Results\SectorResult;
use Scheb\YahooFinanceApi\Screener\EquityQuery;
use Scheb\YahooFinanceApi\Tests\TestCase;
use Scheb\YahooFinanceApi\ValueMapper;

class ApiClientTest extends TestCase
{
    #[Test]
    public function getSector_keyAndRegionGiven_getsExpectedUrl(): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://query{queryServer}.finance.yahoo.com/v1/finance/sectors/healthcare?crumb={crumb}&formatted=false&withReturns=true&lang=en-US&region=DK'
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('sectorResult.json')));

        $result = $this->createClient($contextManager)->getSector('healthcare', 'dk');

        $this->assertInstanceOf(SectorResult::class, $result);
        $this->assertSame('drug-manufacturers-general', $result->getIndustries()[0]->getKey());
    }

    #[Test]
    public function getIndustry_keyAndRegionGiven_getsExpectedUrl(): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://query{queryServer}.finance.yahoo.com/v1/finance/industries/drug-manufacturers-general?crumb={crumb}&formatted=false&withReturns=true&lang=en-US&region=GB'
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('industryResult.json')));

        $result = $this->createClient($contextManager)->getIndustry('drug-manufacturers-general', 'gb');

        $this->assertInstanceOf(IndustryResult::class, $result);
        $this->assertSame('LLY', $result->getTopCompanies()[0]->getSymbol());
        $this->assertSame('healthcare', $result->getSectorKey());
    }

    #[Test]
    public function screen_queryGiven_postsExpectedUrlAndPayload(): void
    {
        $query = new EquityQuery('and', [
            new EquityQuery('eq', ['region', 'dk']),
            new EquityQuery('gte', ['intradaymarketcap', 1_000_000_000]),
        ]);
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://query{queryServer}.finance.yahoo.com/v1/finance/screener?crumb={crumb}&formatted=false&lang=en-US&region=US',
                [
                    'body' => json_encode([
                        'offset' => 10,
                        'count' => 50,
                        'sortField' => 'intradaymarketcap',
                        'sortType' => 'ASC',
                        'userId' => '',
                        'userIdType' => 'guid',
                        'query' => $query->toArray(),
                        'quoteType' => 'EQUITY',
                    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    'headers' => ['Content-Type' => 'application/json'],
                ]
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('screenerResult.json')));

        $result = $this->createClient($contextManager)->screen($query, 10, 50, 'intradaymarketcap', true);

        $this->assertInstanceOf(ScreenerResult::class, $result);
        $this->assertSame(87, $result->getTotal());
    }

    #[Test]
    public function screen_unicodeIndustry_preservesUnicodeInRequestBody(): void
    {
        $query = new EquityQuery('eq', ['industry', 'Drug Manufacturers—General']);
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(static fn (array $options): bool => str_contains($options['body'], 'Drug Manufacturers—General')
                    && !str_contains($options['body'], '\\u2014'))
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('screenerResult.json')));

        $this->createClient($contextManager)->screen($query);
    }

    #[Test]
    public function screenPredefined_screenIdGiven_getsExpectedUrl(): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://query{queryServer}.finance.yahoo.com/v1/finance/screener/predefined/saved?crumb={crumb}&formatted=false&lang=en-US&region=US&scrIds=day_gainers&count=100'
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('screenerResult.json')));

        $result = $this->createClient($contextManager)->screenPredefined('day_gainers', 100);

        $this->assertSame('AAA', $result->getQuotes()[0]['symbol']);
    }

    public static function provideInvalidScreenArguments(): iterable
    {
        yield 'negative offset' => [-1, 25, 'ticker', 'offset'];
        yield 'zero count' => [0, 0, 'ticker', 'count'];
        yield 'excessive count' => [0, 251, 'ticker', 'count'];
        yield 'invalid sort field' => [0, 25, 'symbol', 'sort field'];
    }

    #[Test]
    #[DataProvider('provideInvalidScreenArguments')]
    public function screen_invalidArguments_throwsException(int $offset, int $count, string $sortField, string $message): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->never())->method('request');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->createClient($contextManager)->screen(new EquityQuery('eq', ['region', 'dk']), $offset, $count, $sortField);
    }

    #[Test]
    public function screenPredefined_emptyId_throwsException(): void
    {
        $contextManager = $this->createMock(ContextManagerInterface::class);
        $contextManager->expects($this->never())->method('request');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must not be empty');

        $this->createClient($contextManager)->screenPredefined('  ');
    }

    private function createClient(ContextManagerInterface $contextManager): ApiClient
    {
        return new ApiClient($contextManager, new ResultDecoder(new ValueMapper()));
    }
}
