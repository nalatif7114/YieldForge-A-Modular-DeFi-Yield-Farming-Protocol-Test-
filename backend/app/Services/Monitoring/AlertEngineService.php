<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use App\Models\MonitoringAlert;
use App\Services\Indexer\IndexerHealthService;
use App\Services\Monitoring\DTO\AlertDTO;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class AlertEngineService
{
    public function __construct(
        private readonly IndexerHealthService $indexerHealthService,
        private readonly QueueMonitorService $queueMonitorService,
        private readonly CacheMonitorService $cacheMonitorService,
        private readonly RpcMetricsMonitorService $rpcMonitorService
    ) {}

    /**
     * Evaluate operational rules and persist/update alerts.
     *
     * @return array<int, AlertDTO>
     */
    public function evaluateRules(): array
    {
        $evaluatedAlerts = [];

        try {
            $indexerState = $this->indexerHealthService->getHealth();
            $queueMetrics = $this->queueMonitorService->getMetrics();
            $cacheMetrics = $this->cacheMonitorService->getMetrics();
            $rpcMetrics = $this->rpcMonitorService->getMetrics();

            // 1. Indexer Sync Lag Rule
            if ($indexerState->syncLag > 50) {
                $evaluatedAlerts[] = $this->triggerAlert(
                    ruleName: 'IndexerLagHigh',
                    severity: 'critical',
                    component: 'indexer',
                    message: "Indexer sync lag is critical: {$indexerState->syncLag} blocks behind node.",
                    context: ['lag' => $indexerState->syncLag, 'latest_rpc_block' => $indexerState->latestRpcBlock]
                );
            } elseif ($indexerState->syncLag > 10) {
                $evaluatedAlerts[] = $this->triggerAlert(
                    ruleName: 'IndexerLagWarning',
                    severity: 'warning',
                    component: 'indexer',
                    message: "Indexer sync lag warning: {$indexerState->syncLag} blocks behind node.",
                    context: ['lag' => $indexerState->syncLag]
                );
            } else {
                $this->autoResolveRule('IndexerLagHigh');
                $this->autoResolveRule('IndexerLagWarning');
            }

            // 2. RPC Reachability Rule
            if (!$rpcMetrics->isConnected) {
                $evaluatedAlerts[] = $this->triggerAlert(
                    ruleName: 'RpcNodeDisconnected',
                    severity: 'critical',
                    component: 'rpc',
                    message: "RPC Node connection failed to endpoint [{$rpcMetrics->endpoint}].",
                    context: ['endpoint' => $rpcMetrics->endpoint, 'chain_id' => $rpcMetrics->chainId]
                );
            } else {
                $this->autoResolveRule('RpcNodeDisconnected');
            }

            // 3. Queue Failed Jobs Rule
            if ($queueMetrics->failedJobs > 0) {
                $evaluatedAlerts[] = $this->triggerAlert(
                    ruleName: 'QueueFailedJobs',
                    severity: 'warning',
                    component: 'queue',
                    message: "Queue contains {$queueMetrics->failedJobs} failed job(s).",
                    context: ['failed_jobs' => $queueMetrics->failedJobs]
                );
            } else {
                $this->autoResolveRule('QueueFailedJobs');
            }

            // 4. Cache Efficiency Rule
            if ($cacheMetrics->hitRatioPercentage < 50.0) {
                $evaluatedAlerts[] = $this->triggerAlert(
                    ruleName: 'CacheHitRatioLow',
                    severity: 'info',
                    component: 'cache',
                    message: "Cache hit ratio is low: {$cacheMetrics->hitRatioPercentage}%.",
                    context: ['hit_ratio' => $cacheMetrics->hitRatioPercentage]
                );
            } else {
                $this->autoResolveRule('CacheHitRatioLow');
            }
        } catch (Throwable) {
            // Silently complete
        }

        return $evaluatedAlerts;
    }

    private function triggerAlert(
        string $ruleName,
        string $severity,
        string $component,
        string $message,
        array $context = []
    ): AlertDTO {
        /** @var MonitoringAlert $alert */
        $alert = MonitoringAlert::firstOrCreate(
            [
                'rule_name' => $ruleName,
                'status' => 'active',
            ],
            [
                'severity' => $severity,
                'component' => $component,
                'message' => $message,
                'context' => $context,
            ]
        );

        return AlertDTO::fromModel($alert);
    }

    private function autoResolveRule(string $ruleName): void
    {
        MonitoringAlert::where('rule_name', $ruleName)
            ->where('status', '!=', 'resolved')
            ->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
    }

    /**
     * Get alerts filtered by status and severity.
     *
     * @param string|null $status
     * @param string|null $severity
     * @return array<int, AlertDTO>
     */
    public function getAlerts(?string $status = null, ?string $severity = null): array
    {
        $query = MonitoringAlert::query()->orderByDesc('created_at');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        if ($severity !== null && $severity !== '') {
            $query->where('severity', $severity);
        }

        /** @var Collection<int, MonitoringAlert> $alerts */
        $alerts = $query->get();

        return $alerts->map(fn (MonitoringAlert $model) => AlertDTO::fromModel($model))->all();
    }

    public function acknowledgeAlert(int $id): ?AlertDTO
    {
        /** @var MonitoringAlert|null $alert */
        $alert = MonitoringAlert::find($id);
        if (!$alert) {
            return null;
        }

        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
        ]);

        return AlertDTO::fromModel($alert->fresh());
    }

    public function getRules(): array
    {
        return [
            [
                'rule_name' => 'IndexerLagHigh',
                'component' => 'indexer',
                'severity' => 'critical',
                'threshold' => 'lag > 50 blocks',
                'description' => 'Triggers when block indexer lags significantly behind chain head.',
            ],
            [
                'rule_name' => 'IndexerLagWarning',
                'component' => 'indexer',
                'severity' => 'warning',
                'threshold' => 'lag > 10 blocks',
                'description' => 'Triggers when block indexer lags moderately behind chain head.',
            ],
            [
                'rule_name' => 'RpcNodeDisconnected',
                'component' => 'rpc',
                'severity' => 'critical',
                'threshold' => 'isConnected == false',
                'description' => 'Triggers when JSON-RPC node connection is lost.',
            ],
            [
                'rule_name' => 'QueueFailedJobs',
                'component' => 'queue',
                'severity' => 'warning',
                'threshold' => 'failed_jobs > 0',
                'description' => 'Triggers when background queue jobs fail.',
            ],
            [
                'rule_name' => 'CacheHitRatioLow',
                'component' => 'cache',
                'severity' => 'info',
                'threshold' => 'hit_ratio < 50%',
                'description' => 'Triggers when system cache efficiency drops below 50%.',
            ],
        ];
    }
}
