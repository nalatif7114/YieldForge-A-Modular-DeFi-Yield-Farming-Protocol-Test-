<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Services\Indexer\DTO\IndexerContext;

interface SyncManagerInterface
{
    /**
     * @param IndexerContext $context
     * @return array{from: int, to: int}
     */
    public function determineSyncRange(IndexerContext $context): array;

    public function isSyncNeeded(IndexerContext $context): bool;
}
