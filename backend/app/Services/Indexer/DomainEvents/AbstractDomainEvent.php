<?php

declare(strict_types=1);

namespace App\Services\Indexer\DomainEvents;

use DateTimeInterface;

abstract readonly class AbstractDomainEvent
{
    public function __construct(
        public string $eventName,
        public string $transactionHash,
        public int $logIndex,
        public int $blockNumber,
        public string $contractAddress,
        public ?DateTimeInterface $timestamp = null,
        public array $payload = []
    ) {}

    abstract public function toArray(): array;
}
