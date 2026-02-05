<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Results;

/**
 * @final
 */
class NewsResult implements \JsonSerializable
{
    public function __construct(
        private readonly ?string $uuid,
        private readonly ?string $title,
        private readonly ?string $publisher,
        private readonly ?string $link,
        private readonly ?\DateTimeInterface $pubDate,
        private readonly ?string $type,
        private readonly ?string $summary,
        private readonly ?string $description,
        private readonly ?string $thumbnail,
        private readonly bool $isPremium,
        private readonly bool $isEditorsPick,
        private readonly array $stockTickers,
    ) {}

    public function jsonSerialize(): ?array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'publisher' => $this->publisher,
            'link' => $this->link,
            'pubDate' => $this->pubDate?->format('c'),
            'type' => $this->type,
            'summary' => $this->summary,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'isPremium' => $this->isPremium,
            'isEditorsPick' => $this->isEditorsPick,
            'stockTickers' => $this->stockTickers,
        ];
    }

    public function getUuid(): ?string { return $this->uuid; }
    public function getTitle(): ?string { return $this->title; }
    public function getPublisher(): ?string { return $this->publisher; }
    public function getLink(): ?string { return $this->link; }
    public function getPubDate(): ?\DateTimeInterface { return $this->pubDate; }
    public function getType(): ?string { return $this->type; }
    public function getSummary(): ?string { return $this->summary; }
    public function getDescription(): ?string { return $this->description; }
    public function getThumbnail(): ?string { return $this->thumbnail; }
    public function isPremium(): bool { return $this->isPremium; }
    public function isEditorsPick(): bool { return $this->isEditorsPick; }
    public function getStockTickers(): array { return $this->stockTickers; }
}
