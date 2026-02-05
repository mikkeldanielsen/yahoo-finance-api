<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Context;

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
                if ($try < $this->maxTries) {
                    // Restart session and give it another try when an API exception happened
                    if ($this->retryDelay) {
                        usleep($this->retryDelay * 1000);
                    }
                    $this->renewSession();
                }
            }
        }

        // Final try, throw last exception
        /** @psalm-suppress PossiblyUndefinedVariable */
        throw $e;
    }
}
