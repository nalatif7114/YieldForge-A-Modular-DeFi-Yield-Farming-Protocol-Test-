<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\RebuildAnalyticsJob;
use App\Services\Analytics\Contracts\MetricsAggregatorInterface;
use App\Services\Analytics\SnapshotBuilder;
use Illuminate\Console\Command;

class AnalyticsRebuildCommand extends Command
{
    protected $signature = 'analytics:rebuild {--async}';

    protected $description = 'Rebuild analytics snapshots and time-series aggregations';

    public function handle(SnapshotBuilder $snapshotBuilder, MetricsAggregatorInterface $aggregator): int
    {
        if ((bool) $this->option('async')) {
            RebuildAnalyticsJob::dispatch();
            $this->info('Dispatched RebuildAnalyticsJob.');
            return Command::SUCCESS;
        }

        $this->info('Rebuilding analytics snapshots and aggregations...');
        $snapshotBuilder->buildSnapshot();
        $aggregator->aggregateHourly();
        $aggregator->aggregateDaily();
        $this->info('Analytics rebuild completed successfully.');

        return Command::SUCCESS;
    }
}
