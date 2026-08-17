<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class SectorIndustry implements \JsonSerializable
{
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly ?string $symbol,
        private readonly ?float $marketWeight,
        private readonly array $rawResult,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getMarketWeight(): ?float
    {
        return $this->marketWeight;
    }

    public function getRawResult(): array
    {
        return $this->rawResult;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'marketWeight' => $this->marketWeight,
        ];
    }
}
