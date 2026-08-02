<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Analytics\Contracts\AnalyticsCacheInterface;
use App\Services\Analytics\SnapshotBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyticsSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function handle(SnapshotBuilder $builder, AnalyticsCacheInterface $cache): void
    {
        $builder->buildSnapshot();
        $cache->invalidateAll();
    }
}
