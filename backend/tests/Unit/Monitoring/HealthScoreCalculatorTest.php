<?php

declare(strict_types=1);

namespace Tests\Unit\Monitoring;

use App\Services\Monitoring\Support\HealthScoreCalculator;
use Tests\TestCase;

class HealthScoreCalculatorTest extends TestCase
{
    public function test_health_score_is_100_when_perfect(): void
    {
        $result = HealthScoreCalculator::calculate(
            indexerLag: 0,
            rpcConnected: true,
            rpcLatencyMs: 150.0,
            failedJobsCount: 0,
            pendingJobsCount: 0,
            cacheHitRatio: 98.5,
            criticalAlertsCount: 0,
            warningAlertsCount: 0
        );

        $this->assertEquals(100, $result['score']);
        $this->assertEquals('healthy', $result['status']);
        $this->assertEmpty($result['deductions']);
    }

    public function test_health_score_deducts_points_for_lag_and_failed_jobs(): void
    {
        $result = HealthScoreCalculator::calculate(
            indexerLag: 60, // -30
            rpcConnected: true,
            rpcLatencyMs: 2500.0, // -15
            failedJobsCount: 2, // -10
            pendingJobsCount: 0,
            cacheHitRatio: 90.0,
            criticalAlertsCount: 1, // -15
            warningAlertsCount: 0
        );

        $this->assertEquals(30, $result['score']);
        $this->assertEquals('unhealthy', $result['status']);
        $this->assertArrayHasKey('indexer_lag_critical', $result['deductions']);
    }
}
