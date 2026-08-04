<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MonitoringSnapshot;
use App\Services\Monitoring\MonitoringDashboardService;
use Illuminate\Console\Command;

class MonitoringSnapshotCommand extends Command
{
    protected $signature = 'monitoring:snapshot';

    protected $description = 'Take point-in-time operational snapshot of system performance metrics';

    public function handle(MonitoringDashboardService $dashboardService): int
    {
        $this->info('Collecting system monitoring snapshot...');

        $dashboard = $dashboardService->getDashboard();

        $snapshot = MonitoringSnapshot::create([
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

        $this->info("Snapshot stored successfully (ID #{$snapshot->id}, Health Score: {$snapshot->health_score}).");

        return Command::SUCCESS;
    }
}
