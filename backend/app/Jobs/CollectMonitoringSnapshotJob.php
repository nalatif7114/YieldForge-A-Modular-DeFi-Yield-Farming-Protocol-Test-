<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MonitoringSnapshot;
use App\Services\Monitoring\MonitoringDashboardService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CollectMonitoringSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function handle(MonitoringDashboardService $dashboardService): void
    {
        $dashboard = $dashboardService->getDashboard();

        MonitoringSnapshot::create([
            'health_score' => $dashboard->healthScore,
            'indexer_lag' => $dashboard->indexer['lag'] ?? 0,
            'rpc_latency_ms' => $dashboard->rpc->latencyMs,
            'queue_pending_jobs' => $dashboard->queue->pendingJobs,
            'queue_failed_jobs' => $dashboard->queue->failedJobs,
            'cache_hit_ratio' => $dashboard->cache->hitRatioPercentage,
            'active_alerts_count' => $dashboard->alertsSummary['total_active'] ?? 0,
            'metrics' => $dashboard->toArray(),
            'timestamp' => now(),
        ]);
    }
}
