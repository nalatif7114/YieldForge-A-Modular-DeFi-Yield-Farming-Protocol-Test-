<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\AnalyticsSnapshotJob;
use App\Services\Analytics\SnapshotBuilder;
use Illuminate\Console\Command;

class AnalyticsSnapshotCommand extends Command
{
    protected $signature = 'analytics:snapshot {--async}';

    protected $description = 'Generate an immutable protocol analytics snapshot';

    public function handle(SnapshotBuilder $snapshotBuilder): int
    {
        if ((bool) $this->option('async')) {
            AnalyticsSnapshotJob::dispatch();
            $this->info('Dispatched AnalyticsSnapshotJob.');
            return Command::SUCCESS;
        }

        $this->info('Generating analytics snapshot...');
        $snapshot = $snapshotBuilder->buildSnapshot();
        $this->info("Analytics snapshot #{$snapshot->id} generated successfully.");

        return Command::SUCCESS;
    }
}
