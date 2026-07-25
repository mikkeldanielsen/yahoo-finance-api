<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\Context\ContextManagerInterface;
use Scheb\YahooFinanceApi\ResultDecoder;
use Scheb\YahooFinanceApi\Results\ScreenerResult;
use Scheb\YahooFinanceApi\Screener\EquityQuery;
use Scheb\YahooFinanceApi\Tests\TestCase;
use Scheb\YahooFinanceApi\ValueMapper;

class ApiClientTest extends TestCase
{
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
                    'json' => [
                        'offset' => 10,
                        'count' => 50,
                        'sortField' => 'intradaymarketcap',
                        'sortType' => 'ASC',
                        'userId' => '',
                        'userIdType' => 'guid',
                        'query' => $query->toArray(),
                        'quoteType' => 'EQUITY',
                    ],
                ]
            )
            ->willReturn(new Response(200, [], $this->loadFixtureFile('screenerResult.json')));

        $result = $this->createClient($contextManager)->screen($query, 10, 50, 'intradaymarketcap', true);

        $this->assertInstanceOf(ScreenerResult::class, $result);
        $this->assertSame(87, $result->getTotal());
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
