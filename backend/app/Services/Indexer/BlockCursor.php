<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\IndexedBlock;
use App\Models\ProjectionCheckpoint;
use App\Services\Indexer\DTO\IndexerContext;

class BlockCursor
{
    public function getLatestIndexedBlock(IndexerContext $context): int
    {
        /** @var IndexedBlock|null $latest */
        $latest = IndexedBlock::query()
            ->where('chain_id', $context->chainId)
            ->where('status', 'processed')
            ->orderByDesc('block_number')
            ->first();

        return $latest ? (int) $latest->block_number : 0;
    }

    public function updateCursor(
        IndexerContext $context,
        int $blockNumber,
        ?string $blockHash = null,
        ?string $parentHash = null,
        int $eventsCount = 0
    ): IndexedBlock {
        return IndexedBlock::updateOrCreate(
            [
                'chain_id' => $context->chainId,
                'block_number' => $blockNumber,
            ],
            [
                'network' => $context->network,
                'block_hash' => $blockHash,
                'parent_hash' => $parentHash,
                'status' => 'processed',
                'events_count' => $eventsCount,
                'timestamp' => now(),
            ]
        );
    }

    public function updateCheckpoint(
        string $projectionName,
        int $blockNumber,
        int $transactionIndex = 0,
        int $logIndex = 0,
        string $version = '1.0.0'
    ): ProjectionCheckpoint {
        return ProjectionCheckpoint::updateOrCreate(
            ['projection_name' => $projectionName],
            [
                'last_processed_block' => $blockNumber,
                'last_transaction_index' => $transactionIndex,
                'last_log_index' => $logIndex,
                'projection_version' => $version,
            ]
        );
    }

    public function getCheckpoint(string $projectionName): ?ProjectionCheckpoint
    {
        return ProjectionCheckpoint::where('projection_name', $projectionName)->first();
    }
}
