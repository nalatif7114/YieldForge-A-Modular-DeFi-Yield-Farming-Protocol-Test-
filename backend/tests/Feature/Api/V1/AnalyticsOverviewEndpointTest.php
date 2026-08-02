<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsOverviewEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_analytics_overview_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/overview');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'kpis' => [
                        'total_value_locked_formatted',
                        'daily_growth_percentage',
                        'weekly_growth_percentage',
                        'monthly_growth_percentage',
                        'active_users',
                        'new_users',
                        'returning_users',
                        'reward_efficiency',
                        'capital_efficiency',
                    ],
                    'benchmarks' => [
                        'tvl_24h_change_percentage',
                        'tvl_7d_change_percentage',
                        'apy_30d_average',
                        'historical_high_tvl_formatted',
                        'historical_low_tvl_formatted',
                    ],
                    'health_score',
                    'timestamp',
                ],
            ]);
    }
}
