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
        private readonly ?int $providerPublishTime,
        private readonly ?string $type
    ) {}

    public function jsonSerialize(): ?array
    {
        return array_merge(
            get_class_vars(self::class),
            get_object_vars($this)
        );
    }

    public function getUuid(): ?string { return $this->uuid; }
    public function getTitle(): ?string { return $this->title; }
    public function getPublisher(): ?string { return $this->publisher; }
    public function getLink(): ?string { return $this->link; }
    public function getProviderPublishTime(): ?int { return $this->providerPublishTime; }
    public function getType(): ?string { return $this->type; }
}

