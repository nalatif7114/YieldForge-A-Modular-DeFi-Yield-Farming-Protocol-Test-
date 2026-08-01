<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncHistoricalBlocksJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $fromBlock,
        public int $toBlock
    ) {}

    public function handle(BlockchainIndexerInterface $indexer): void
    {
        $indexer->syncRange($this->fromBlock, $this->toBlock);
    }
}
