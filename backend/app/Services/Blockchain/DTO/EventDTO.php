<?php

declare(strict_types=1);

namespace App\Services\Blockchain\DTO;

readonly class EventDTO
{
    /**
     * @param string $eventName
     * @param string $contractAddress
     * @param string $transactionHash
     * @param int $blockNumber
     * @param int $logIndex
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public string $eventName,
        public string $contractAddress,
        public string $transactionHash,
        public int $blockNumber,
        public int $logIndex,
        public array $parameters
    ) {}

    /**
     * Convert DTO to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_name' => $this->eventName,
            'contract_address' => $this->contractAddress,
            'transaction_hash' => $this->transactionHash,
            'block_number' => $this->blockNumber,
            'log_index' => $this->logIndex,
            'parameters' => $this->parameters,
        ];
    }
}
