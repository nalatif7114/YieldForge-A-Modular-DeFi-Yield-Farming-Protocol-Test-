<?php

declare(strict_types=1);

namespace App\Services\Indexer\Contracts;

use App\Services\Indexer\DTO\IndexerContext;
use App\Services\Indexer\DTO\SyncResultDTO;

interface BlockchainIndexerInterface
{
    public function syncLatest(?IndexerContext $context = null): SyncResultDTO;

    public function syncRange(int $fromBlock, int $toBlock, ?IndexerContext $context = null): SyncResultDTO;

    public function getContext(): IndexerContext;
}
