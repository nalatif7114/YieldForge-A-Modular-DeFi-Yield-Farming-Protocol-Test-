<?php

declare(strict_types=1);

namespace App\Services\Monitoring\Support;

class HealthScoreCalculator
{
    /**
     * Calculate health score (0-100) and return score with status category.
     *
     * @param int $indexerLag
     * @param bool $rpcConnected
     * @param float $rpcLatencyMs
     * @param int $failedJobsCount
     * @param int $pendingJobsCount
     * @param float $cacheHitRatio
     * @param int $criticalAlertsCount
     * @param int $warningAlertsCount
     * @return array{score: int, status: string, deductions: array<string, int>}
     */
    public static function calculate(
        int $indexerLag,
        bool $rpcConnected,
        float $rpcLatencyMs,
        int $failedJobsCount,
        int $pendingJobsCount,
        float $cacheHitRatio,
        int $criticalAlertsCount,
        int $warningAlertsCount
    ): array {
        $score = 100;
        $deductions = [];

        // 1. Indexer Sync Lag Penalties
        if ($indexerLag > 50) {
            $score -= 30;
            $deductions['indexer_lag_critical'] = 30;
        } elseif ($indexerLag > 10) {
            $score -= 15;
            $deductions['indexer_lag_warning'] = 15;
        } elseif ($indexerLag > 0) {
            $score -= 5;
            $deductions['indexer_lag_minor'] = 5;
        }

        // 2. RPC Reachability & Latency Penalties
        if (!$rpcConnected) {
            $score -= 25;
            $deductions['rpc_disconnected'] = 25;
        } elseif ($rpcLatencyMs > 2000) {
            $score -= 15;
            $deductions['rpc_high_latency'] = 15;
        } elseif ($rpcLatencyMs > 1000) {
            $score -= 5;
            $deductions['rpc_moderate_latency'] = 5;
        }

        // 3. Queue Penalties
        if ($failedJobsCount > 0) {
            $penalty = min(20, $failedJobsCount * 5);
            $score -= $penalty;
            $deductions['queue_failed_jobs'] = $penalty;
        }
        if ($pendingJobsCount > 1000) {
            $score -= 10;
            $deductions['queue_backlog'] = 10;
        }

        // 4. Cache Efficiency Penalties
        if ($cacheHitRatio < 50.0) {
            $score -= 10;
            $deductions['cache_low_hit_ratio'] = 10;
        }

        // 5. Active Alert Penalties
        if ($criticalAlertsCount > 0) {
            $penalty = min(30, $criticalAlertsCount * 15);
            $score -= $penalty;
            $deductions['active_critical_alerts'] = $penalty;
        }
        if ($warningAlertsCount > 0) {
            $penalty = min(15, $warningAlertsCount * 5);
            $score -= $penalty;
            $deductions['active_warning_alerts'] = $penalty;
        }

        $finalScore = max(0, min(100, $score));

        $status = match (true) {
            $finalScore >= 90 => 'healthy',
            $finalScore >= 70 => 'degraded',
            default => 'unhealthy',
        };

        return [
            'score' => $finalScore,
            'status' => $status,
            'deductions' => $deductions,
        ];
    }
}
