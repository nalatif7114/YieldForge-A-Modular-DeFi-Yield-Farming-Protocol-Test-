<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use App\Models\MonitoringAlert;
use App\Models\ProtocolStatistic;
use App\Services\Indexer\IndexerHealthService;
use App\Services\Monitoring\DTO\MonitoringDashboardDTO;
use App\Services\Monitoring\Support\HealthScoreCalculator;

class MonitoringDashboardService
{
    public function __construct(
        private readonly IndexerHealthService $indexerHealthService,
        private readonly QueueMonitorService $queueMonitorService,
        private readonly CacheMonitorService $cacheMonitorService,
        private readonly RpcMetricsMonitorService $rpcMonitorService
    ) {}

    public function getDashboard(): MonitoringDashboardDTO
    {
        $indexerState = $this->indexerHealthService->getHealth();
        $queueMetrics = $this->queueMonitorService->getMetrics();
        $cacheMetrics = $this->cacheMonitorService->getMetrics();
        $rpcMetrics = $this->rpcMonitorService->getMetrics();

        $activeAlerts = MonitoringAlert::where('status', 'active')->get();
        $criticalCount = $activeAlerts->where('severity', 'critical')->count();
        $warningCount = $activeAlerts->where('severity', 'warning')->count();
        $infoCount = $activeAlerts->where('severity', 'info')->count();

        $healthResult = HealthScoreCalculator::calculate(
            indexerLag: $indexerState->syncLag,
            rpcConnected: $rpcMetrics->isConnected,
            rpcLatencyMs: $rpcMetrics->latencyMs,
            failedJobsCount: $queueMetrics->failedJobs,
            pendingJobsCount: $queueMetrics->pendingJobs,
            cacheHitRatio: $cacheMetrics->hitRatioPercentage,
            criticalAlertsCount: $criticalCount,
            warningAlertsCount: $warningCount
        );

        /** @var ProtocolStatistic|null $protocolStats */
        $protocolStats = ProtocolStatistic::first();
        $protocolKpis = [
            'tvl_formatted' => $protocolStats?->total_value_locked_formatted ?? '0',
            'total_stakers' => $protocolStats?->total_stakers_count ?? 0,
            'total_events_processed' => $protocolStats?->total_events_processed ?? 0,
            'latest_indexed_block' => $protocolStats?->latest_indexed_block ?? 0,
        ];

        return new MonitoringDashboardDTO(
            healthScore: $healthResult['score'],
            overallStatus: $healthResult['status'],
            indexer: $indexerState->toArray(),
            queue: $queueMetrics,
            cache: $cacheMetrics,
            rpc: $rpcMetrics,
            alertsSummary: [
                'total_active' => $activeAlerts->count(),
                'critical' => $criticalCount,
                'warning' => $warningCount,
                'info' => $infoCount,
            ],
            protocolKpis: $protocolKpis,
            timestamp: now()->toIso8601String()
        );
    }
}
