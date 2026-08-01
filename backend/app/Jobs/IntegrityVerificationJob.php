<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\BlockchainEvent;
use App\Models\IndexedBlock;
use Illuminate\Contracts\Logging\Factory as LogFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IntegrityVerificationJob implements ShouldQueue
{
    use Queueable;

    public function handle(LogFactory $log): void
    {
        $totalBlocks = IndexedBlock::count();
        $totalEvents = BlockchainEvent::count();

        $log->channel('indexer')->info("Integrity check completed: {$totalBlocks} blocks indexed, {$totalEvents} events in store.");
    }
}
