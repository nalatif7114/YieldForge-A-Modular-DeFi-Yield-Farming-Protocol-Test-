<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsHealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_analytics_health_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'status',
                    'health_score',
                    'last_snapshot',
                    'analytics_delay_ms',
                    'snapshots_generated',
                    'aggregation_duration_ms',
                    'cache_hit_ratio',
                    'average_query_time_ms',
                    'storage_usage_mb',
                    'historical_coverage_days',
                    'timestamp',
                ],
            ]);
    }
}
