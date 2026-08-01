<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Models\IndexedBlock;
use App\Models\ProjectionCheckpoint;
use App\Services\Indexer\Contracts\ReorgDetectorInterface;
use App\Services\Indexer\DTO\IndexerContext;
use Illuminate\Log\LogManager;

class ReorgDetector implements ReorgDetectorInterface
{
    public function __construct(
        private readonly LogManager $log
    ) {}

    public function detectReorg(IndexerContext $context, int $blockNumber, ?string $parentHash): ?int
    {
        if ($blockNumber <= 1 || $parentHash === null || $parentHash === '') {
            return null;
        }

        /** @var IndexedBlock|null $previousBlock */
        $previousBlock = IndexedBlock::query()
            ->where('chain_id', $context->chainId)
            ->where('block_number', $blockNumber - 1)
            ->where('status', 'processed')
            ->first();

        if ($previousBlock && $previousBlock->block_hash !== null && strtolower($previousBlock->block_hash) !== strtolower($parentHash)) {
            $this->log->channel('indexer')->warning('Reorg detected!', [
                'block_number' => $blockNumber,
                'db_parent_hash' => $previousBlock->block_hash,
                'incoming_parent_hash' => $parentHash,
            ]);

            return (int) $previousBlock->block_number - 1;
        }

        return null;
    }

    public function rollbackTo(IndexerContext $context, int $commonAncestorBlock): void
    {
        $this->log->channel('indexer')->info("Rolling back state to block [{$commonAncestorBlock}]");

        // Mark blocks as reverted
        IndexedBlock::query()
            ->where('chain_id', $context->chainId)
            ->where('block_number', '>', $commonAncestorBlock)
            ->update(['status' => 'reverted']);

        // Mark events as removed
        BlockchainEvent::query()
            ->where('chain_id', $context->chainId)
            ->where('block_number', '>', $commonAncestorBlock)
            ->update(['removed' => true]);

        // Rollback checkpoints
        ProjectionCheckpoint::query()
            ->where('last_processed_block', '>', $commonAncestorBlock)
            ->update(['last_processed_block' => $commonAncestorBlock]);
    }
}
