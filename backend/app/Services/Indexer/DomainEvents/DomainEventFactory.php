<?php

declare(strict_types=1);

namespace App\Services\Indexer\DomainEvents;

use App\Models\BlockchainEvent;
use App\Services\Blockchain\DTO\EventDTO;

class DomainEventFactory
{
    /**
     * Create DomainEvent from BlockchainEvent Eloquent model.
     *
     * @param BlockchainEvent $event
     * @return AbstractDomainEvent
     */
    public function fromModel(BlockchainEvent $event): AbstractDomainEvent
    {
        $name = $event->event_name;
        $payload = is_array($event->payload) ? $event->payload : [];
        $txHash = $event->transaction_hash;
        $logIndex = $event->log_index;
        $blockNum = $event->block_number;
        $contractAddr = $event->contract_address;
        $timestamp = $event->timestamp;

        return match ($name) {
            'Staked' => new StakedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                user: (string) ($payload['user'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                timestamp: $timestamp,
                payload: $payload
            ),
            'Withdrawn' => new WithdrawnDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                user: (string) ($payload['user'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                timestamp: $timestamp,
                payload: $payload
            ),
            'Transfer' => new TransferDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                from: (string) ($payload['from'] ?? ''),
                to: (string) ($payload['to'] ?? ''),
                valueRaw: (string) ($payload['value_raw'] ?? '0'),
                valueFormatted: (string) ($payload['value_formatted'] ?? '0'),
                timestamp: $timestamp,
                payload: $payload
            ),
            'TokensMinted' => new TokensMintedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                to: (string) ($payload['to'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                timestamp: $timestamp,
                payload: $payload
            ),
            'TokensBurned' => new TokensBurnedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                from: (string) ($payload['from'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                timestamp: $timestamp,
                payload: $payload
            ),
            'Paused' => new PausedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                account: (string) ($payload['account'] ?? ''),
                timestamp: $timestamp,
                payload: $payload
            ),
            'Unpaused' => new UnpausedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                account: (string) ($payload['account'] ?? ''),
                timestamp: $timestamp,
                payload: $payload
            ),
            default => new GenericDomainEvent(
                eventName: $name,
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                timestamp: $timestamp,
                payload: $payload
            ),
        };
    }

    /**
     * Create DomainEvent from EventDTO.
     *
     * @param EventDTO $dto
     * @return AbstractDomainEvent
     */
    public function fromDto(EventDTO $dto): AbstractDomainEvent
    {
        $name = $dto->eventName;
        $payload = $dto->parameters;
        $txHash = $dto->transactionHash;
        $logIndex = $dto->logIndex;
        $blockNum = $dto->blockNumber;
        $contractAddr = $dto->contractAddress;

        return match ($name) {
            'Staked' => new StakedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                user: (string) ($payload['user'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                payload: $payload
            ),
            'Withdrawn' => new WithdrawnDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                user: (string) ($payload['user'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                payload: $payload
            ),
            'Transfer' => new TransferDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                from: (string) ($payload['from'] ?? ''),
                to: (string) ($payload['to'] ?? ''),
                valueRaw: (string) ($payload['value_raw'] ?? '0'),
                valueFormatted: (string) ($payload['value_formatted'] ?? '0'),
                payload: $payload
            ),
            'TokensMinted' => new TokensMintedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                to: (string) ($payload['to'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                payload: $payload
            ),
            'TokensBurned' => new TokensBurnedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                from: (string) ($payload['from'] ?? ''),
                amountRaw: (string) ($payload['amount_raw'] ?? '0'),
                amountFormatted: (string) ($payload['amount_formatted'] ?? '0'),
                payload: $payload
            ),
            'Paused' => new PausedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                account: (string) ($payload['account'] ?? ''),
                payload: $payload
            ),
            'Unpaused' => new UnpausedDomainEvent(
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                account: (string) ($payload['account'] ?? ''),
                payload: $payload
            ),
            default => new GenericDomainEvent(
                eventName: $name,
                transactionHash: $txHash,
                logIndex: $logIndex,
                blockNumber: $blockNum,
                contractAddress: $contractAddr,
                payload: $payload
            ),
        };
    }
}
