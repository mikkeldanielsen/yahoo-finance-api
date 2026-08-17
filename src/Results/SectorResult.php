<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class SectorResult implements \JsonSerializable
{
    /**
     * @param SectorIndustry[] $industries
     * @param DomainCompany[]  $topCompanies
     */
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly ?string $symbol,
        private readonly array $overview,
        private readonly array $industries,
        private readonly array $topCompanies,
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

    public function getOverview(): array
    {
        return $this->overview;
    }

    /** @return SectorIndustry[] */
    public function getIndustries(): array
    {
        return $this->industries;
    }

    /** @return DomainCompany[] */
    public function getTopCompanies(): array
    {
        return $this->topCompanies;
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
            'overview' => $this->overview,
            'industries' => $this->industries,
            'topCompanies' => $this->topCompanies,
        ];
    }
}
