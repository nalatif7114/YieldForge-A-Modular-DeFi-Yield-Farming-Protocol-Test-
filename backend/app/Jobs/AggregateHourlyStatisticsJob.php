<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Analytics\Contracts\MetricsAggregatorInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AggregateHourlyStatisticsJob implements ShouldQueue
{
    use Queueable;

    public function handle(MetricsAggregatorInterface $aggregator): void
    {
        $aggregator->aggregateHourly();
    }
}
