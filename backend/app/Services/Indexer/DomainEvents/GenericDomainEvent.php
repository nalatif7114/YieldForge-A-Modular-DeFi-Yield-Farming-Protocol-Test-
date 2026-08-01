<?php

declare(strict_types=1);

namespace App\Services\Indexer\DomainEvents;

use DateTimeInterface;

readonly class GenericDomainEvent extends AbstractDomainEvent
{
    public function __construct(
        string $eventName,
        string $transactionHash,
        int $logIndex,
        int $blockNumber,
        string $contractAddress,
        ?DateTimeInterface $timestamp = null,
        array $payload = []
    ) {
        parent::__construct($eventName, $transactionHash, $logIndex, $blockNumber, $contractAddress, $timestamp, $payload);
    }

    public function toArray(): array
    {
        return array_merge([
            'event_name' => $this->eventName,
            'transaction_hash' => $this->transactionHash,
            'log_index' => $this->logIndex,
            'block_number' => $this->blockNumber,
            'contract_address' => $this->contractAddress,
        ], $this->payload);
    }
}
