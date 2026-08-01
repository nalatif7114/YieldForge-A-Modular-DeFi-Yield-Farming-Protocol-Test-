<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Blockchain\Contracts\EventServiceInterface;
use App\Services\Indexer\DTO\IndexerContext;
use Illuminate\Log\LogManager;
use Throwable;

class LogProcessor
{
    public function __construct(
        private readonly EventServiceInterface $eventService,
        private readonly LogManager $log
    ) {}

    /**
     * Fetch logs for block range, store in blockchain_events table, return list of newly saved models.
     *
     * @param IndexerContext $context
     * @param int $fromBlock
     * @param int $toBlock
     * @return array<int, BlockchainEvent>
     */
    public function process(IndexerContext $context, int $fromBlock, int $toBlock): array
    {
        try {
            $eventDtos = $this->eventService->getEvents(
                contractKey: null,
                eventName: null,
                fromBlock: $fromBlock,
                toBlock: $toBlock,
                limit: 500
            );

            $savedEvents = [];

            foreach ($eventDtos as $dto) {
                /** @var BlockchainEvent $model */
                $model = BlockchainEvent::firstOrCreate(
                    [
                        'transaction_hash' => $dto->transactionHash,
                        'log_index' => $dto->logIndex,
                    ],
                    [
                        'chain_id' => $context->chainId,
                        'network' => $context->network,
                        'block_number' => $dto->blockNumber,
                        'contract_address' => strtolower($dto->contractAddress),
                        'event_name' => $dto->eventName,
                        'event_version' => '1.0.0',
                        'contract_version' => '1.0.0',
                        'payload' => $dto->parameters,
                        'removed' => false,
                        'timestamp' => now(),
                    ]
                );

                $savedEvents[] = $model;
            }

            return $savedEvents;
        } catch (Throwable $e) {
            $this->log->channel('indexer')->error("LogProcessor error processing blocks {$fromBlock}-{$toBlock}: {$e->getMessage()}");
            return [];
        }
    }
}
