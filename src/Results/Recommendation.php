<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

class Recommendation implements \JsonSerializable
{
    private $symbol;
    private $score;
    public function __construct(string $symbol, ?float $score)
    {
        $this->symbol = $symbol;
        $this->score = $score;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getSymbol(): string {
        return $this->symbol;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }
}
