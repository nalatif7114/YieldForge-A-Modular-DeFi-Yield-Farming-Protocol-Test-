<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ReplayEventsJob;
use App\Services\Indexer\Contracts\ReplayEngineInterface;
use Illuminate\Console\Command;

class BlockchainReplayCommand extends Command
{
    protected $signature = 'blockchain:replay {--from=0} {--async}';

    protected $description = 'Rebuild database projections from stored Event Store without querying RPC';

    public function handle(ReplayEngineInterface $replayEngine): int
    {
        $fromBlock = (int) $this->option('from');
        $isAsync = (bool) $this->option('async');

        if ($isAsync) {
            ReplayEventsJob::dispatch($fromBlock);
            $this->info("Dispatched ReplayEventsJob from block [{$fromBlock}].");
            return Command::SUCCESS;
        }

        $this->info("Replaying events from block [{$fromBlock}]...");
        $count = $replayEngine->replay($fromBlock);
        $this->info("Replay finished: {$count} events replayed.");

        return Command::SUCCESS;
    }
}
