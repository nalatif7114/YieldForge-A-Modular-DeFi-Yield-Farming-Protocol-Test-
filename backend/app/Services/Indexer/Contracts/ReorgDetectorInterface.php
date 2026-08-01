<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Services\Indexer\DTO\IndexerContext;

interface ReorgDetectorInterface
{
    /**
     * Check if a reorg occurred. Returns divergence block number if reorg detected, null otherwise.
     *
     * @param IndexerContext $context
     * @param int $blockNumber
     * @param string|null $parentHash
     * @return int|null Divergence block number or null if clean
     */
    public function detectReorg(IndexerContext $context, int $blockNumber, ?string $parentHash): ?int;

    /**
     * Rollback indexed blocks, events, and projection checkpoints back to specified block.
     *
     * @param IndexerContext $context
     * @param int $commonAncestorBlock
     */
    public function rollbackTo(IndexerContext $context, int $commonAncestorBlock): void;
}
