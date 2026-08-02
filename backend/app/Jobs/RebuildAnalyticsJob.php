<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Analytics\Contracts\MetricsAggregatorInterface;
use App\Services\Analytics\SnapshotBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RebuildAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function handle(SnapshotBuilder $snapshotBuilder, MetricsAggregatorInterface $aggregator): void
    {
        $snapshotBuilder->buildSnapshot();
        $aggregator->aggregateHourly();
        $aggregator->aggregateDaily();
    }
}
