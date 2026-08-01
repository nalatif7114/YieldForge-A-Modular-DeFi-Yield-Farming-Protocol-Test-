<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncLatestBlockJob implements ShouldQueue
{
    use Queueable;

    public function handle(BlockchainIndexerInterface $indexer): void
    {
        $indexer->syncLatest();
    }
}
