<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SyncHistoricalBlocksJob;
use App\Jobs\SyncLatestBlockJob;
use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use Illuminate\Console\Command;

class BlockchainSyncCommand extends Command
{
    protected $signature = 'blockchain:sync {--from=} {--to=} {--async}';

    protected $description = 'Synchronize smart contract events from blockchain into indexer store';

    public function handle(BlockchainIndexerInterface $indexer): int
    {
        $from = $this->option('from') !== null ? (int) $this->option('from') : null;
        $to = $this->option('to') !== null ? (int) $this->option('to') : null;
        $isAsync = (bool) $this->option('async');

        if ($from !== null && $to !== null) {
            if ($isAsync) {
                SyncHistoricalBlocksJob::dispatch($from, $to);
                $this->info("Dispatched SyncHistoricalBlocksJob for range {$from}-{$to}.");
                return Command::SUCCESS;
            }

            $this->info("Syncing range {$from}-{$to}...");
            $result = $indexer->syncRange($from, $to);
            $this->info("Sync completed: {$result->blocksProcessed} blocks, {$result->eventsIndexed} events in {$result->durationMs}ms.");
            return Command::SUCCESS;
        }

        if ($isAsync) {
            SyncLatestBlockJob::dispatch();
            $this->info('Dispatched SyncLatestBlockJob.');
            return Command::SUCCESS;
        }

        $this->info('Syncing latest blocks...');
        $result = $indexer->syncLatest();
        $this->info("Sync completed: {$result->blocksProcessed} blocks, {$result->eventsIndexed} events in {$result->durationMs}ms.");

        return Command::SUCCESS;
    }
}
