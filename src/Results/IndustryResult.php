<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class IndustryResult implements \JsonSerializable
{
    /** @param DomainCompany[] $topCompanies */
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly ?string $symbol,
        private readonly ?string $sectorKey,
        private readonly ?string $sectorName,
        private readonly array $overview,
        private readonly array $topCompanies,
        private readonly array $topPerformingCompanies,
        private readonly array $topGrowthCompanies,
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

    public function getSectorKey(): ?string
    {
        return $this->sectorKey;
    }

    public function getSectorName(): ?string
    {
        return $this->sectorName;
    }

    public function getOverview(): array
    {
        return $this->overview;
    }

    /** @return DomainCompany[] */
    public function getTopCompanies(): array
    {
        return $this->topCompanies;
    }

    public function getTopPerformingCompanies(): array
    {
        return $this->topPerformingCompanies;
    }

    public function getTopGrowthCompanies(): array
    {
        return $this->topGrowthCompanies;
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
            'sectorKey' => $this->sectorKey,
            'sectorName' => $this->sectorName,
            'overview' => $this->overview,
            'topCompanies' => $this->topCompanies,
            'topPerformingCompanies' => $this->topPerformingCompanies,
            'topGrowthCompanies' => $this->topGrowthCompanies,
        ];
    }
}
