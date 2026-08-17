<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class DomainCompany implements \JsonSerializable
{
    public function __construct(
        private readonly string $symbol,
        private readonly ?string $name,
        private readonly ?string $rating,
        private readonly ?float $marketWeight,
        private readonly array $rawResult,
    ) {
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRating(): ?string
    {
        return $this->rating;
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
            'symbol' => $this->symbol,
            'name' => $this->name,
            'rating' => $this->rating,
            'marketWeight' => $this->marketWeight,
        ];
    }
}
