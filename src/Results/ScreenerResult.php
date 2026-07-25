<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class ScreenerResult implements \JsonSerializable
{
    public function __construct(
        private readonly int $start,
        private readonly int $count,
        private readonly int $total,
        private readonly array $quotes,
        private readonly array $metadata,
        private readonly array $rawResult,
    ) {
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getQuotes(): array
    {
        return $this->quotes;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getRawResult(): array
    {
        return $this->rawResult;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            get_class_vars(self::class),
            get_object_vars($this)
        );
    }
}
