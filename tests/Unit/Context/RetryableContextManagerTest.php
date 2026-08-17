<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Context;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Scheb\YahooFinanceApi\Context\ContextManagerInterface;
use Scheb\YahooFinanceApi\Context\RetryableContextManager;
use Scheb\YahooFinanceApi\Tests\TestCase;

class RetryableContextManagerTest extends TestCase
{
    public const MAX_TRIES = 3;
    public const RETRY_DELAY = 0;

    private MockObject|ContextManagerInterface $mockContextManager;
    private RetryableContextManager $retryableContextManager;

    protected function setUp(): void
    {
        $this->mockContextManager = $this->createMock(ContextManagerInterface::class);
        $this->retryableContextManager = new RetryableContextManager(
            $this->mockContextManager,
            self::MAX_TRIES,
            self::RETRY_DELAY,
        );
    }

    #[Test]
    public function renewSession_whenCalled_delegatesToWrappedContextManager(): void
    {
        $this->mockContextManager
            ->expects($this->once())
            ->method('renewSession');

        $this->retryableContextManager->renewSession();
    }

    #[Test]
    public function request_successfulOnFirstTry_returnsResponse(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $this->mockContextManager
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturn($expectedResponse);

        $result = $this->retryableContextManager->request('GET', 'https://example.com');

        $this->assertSame($expectedResponse, $result);
    }

    #[Test]
    public function request_withOptions_forwardsOptionsOnEveryAttempt(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $options = ['json' => ['query' => ['operator' => 'EQ']]];

        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('request')
            ->with('POST', 'https://example.com', $options)
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new \Exception('Network error')),
                $expectedResponse
            );

        $result = $this->retryableContextManager->request('POST', 'https://example.com', $options);

        $this->assertSame($expectedResponse, $result);
    }

    #[Test]
    public function request_failsFirstTryThenSucceeds_retriesAndReturnsResponse(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $exception = new \Exception('Network error');

        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception),
                $expectedResponse
            );

        $this->mockContextManager
            ->expects($this->once())
            ->method('renewSession');

        $result = $this->retryableContextManager->request('GET', 'https://example.com');

        $this->assertSame($expectedResponse, $result);
    }

    #[Test]
    public function request_failsAllTries_throwsLastException(): void
    {
        $exception1 = new \Exception('First error');
        $exception2 = new \Exception('Second error');
        $exception3 = new \Exception('Third error');

        $this->mockContextManager
            ->expects($this->exactly(3))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception1),
                $this->throwException($exception2),
                $this->throwException($exception3)
            );

        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('renewSession');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Third error');

        $this->retryableContextManager->request('GET', 'https://example.com');
    }

    #[Test]
    public function request_withRetryDelay_retryDelayIsApplied(): void
    {
        $retryableContextManager = new RetryableContextManager(
            $this->mockContextManager,
            2,  // maxTries
            500 // retryDelay (500ms)
        );

        $exception = new \Exception('Network error');

        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('request')
            ->with('GET', 'https://example.com')
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception),
                $this->createMock(ResponseInterface::class)
            );

        $startTime = microtime(true);
        $retryableContextManager->request('GET', 'https://example.com');
        $endTime = microtime(true);

        // Verify that some delay was applied (allowing for some tolerance)
        $executionTime = ($endTime - $startTime) * 1000000; // Convert to microseconds
        $this->assertGreaterThan(500, $executionTime); // At least 500ms should have passed
    }

    #[Test]
    public function request_rateLimited_renewsSessionAndRetries(): void
    {
        $exception = new ClientException(
            'Rate limited',
            new Request('GET', 'https://example.com'),
            new Response(429, ['Retry-After' => '0'])
        );
        $response = $this->createMock(ResponseInterface::class);
        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls($this->throwException($exception), $response);
        $this->mockContextManager->expects($this->once())->method('renewSession');

        $this->assertSame($response, $this->retryableContextManager->request('GET', 'https://example.com'));
    }

    #[Test]
    public function request_unauthorized_renewsSessionAndRetries(): void
    {
        $exception = new ClientException(
            'Unauthorized',
            new Request('GET', 'https://example.com'),
            new Response(401)
        );
        $response = $this->createMock(ResponseInterface::class);
        $this->mockContextManager
            ->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls($this->throwException($exception), $response);
        $this->mockContextManager->expects($this->once())->method('renewSession');

        $this->assertSame($response, $this->retryableContextManager->request('GET', 'https://example.com'));
    }

    #[Test]
    public function request_permanentClientError_doesNotRetry(): void
    {
        $exception = new ClientException(
            'Not found',
            new Request('GET', 'https://example.com'),
            new Response(404)
        );
        $this->mockContextManager
            ->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
        $this->mockContextManager->expects($this->never())->method('renewSession');

        $this->expectException(ClientException::class);
        $this->retryableContextManager->request('GET', 'https://example.com');
    }
}
