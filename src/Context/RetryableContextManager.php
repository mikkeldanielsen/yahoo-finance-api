<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * @final
 */
class RetryableContextManager implements ContextManagerInterface
{
    public function __construct(
        private readonly ContextManagerInterface $contextManager,
        private readonly int $maxTries,
        private readonly int $retryDelay,
    ) {
    }

    public function renewSession(): void
    {
        $this->contextManager->renewSession();
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        for ($try = 1; $try <= $this->maxTries; ++$try) {
            try {
                return $this->contextManager->request($method, $url, $options);
            } catch (\Exception $e) {
                if ($try >= $this->maxTries || !$this->isRetryable($e)) {
                    throw $e;
                }

                if ($this->retryDelay > 0) {
                    usleep($this->retryDelay * 1000);
                }

                $this->renewSession();
            }
        }

        // Final try, throw last exception
        /** @psalm-suppress PossiblyUndefinedVariable */
        throw $e;
    }

    private function isRetryable(\Exception $exception): bool
    {
        if (!$exception instanceof RequestException) {
            return true;
        }

        $response = $exception->getResponse();
        if (null === $response) {
            return true;
        }

        $statusCode = $response->getStatusCode();

        return \in_array($statusCode, [401, 403, 408, 425, 429], true) || $statusCode >= 500;
    }
}
