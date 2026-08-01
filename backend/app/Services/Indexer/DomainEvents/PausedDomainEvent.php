<?php

declare(strict_types=1);

namespace App\Services\Indexer\DomainEvents;

use DateTimeInterface;

readonly class PausedDomainEvent extends AbstractDomainEvent
{
    public function __construct(
        string $transactionHash,
        int $logIndex,
        int $blockNumber,
        string $contractAddress,
        public string $account,
        ?DateTimeInterface $timestamp = null,
        array $payload = []
    ) {
        parent::__construct('Paused', $transactionHash, $logIndex, $blockNumber, $contractAddress, $timestamp, $payload);
    }

    public function toArray(): array
    {
        return [
            'event_name' => $this->eventName,
            'transaction_hash' => $this->transactionHash,
            'log_index' => $this->logIndex,
            'block_number' => $this->blockNumber,
            'contract_address' => $this->contractAddress,
            'account' => $this->account,
        ];
    }
}
