<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoolAnalyticsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_pools_analytics_endpoints(): void
    {
        $responseAll = $this->getJson('/api/v1/analytics/pools');
        $responseAll->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'pool_id',
                        'tvl_raw',
                        'tvl_formatted',
                        'active_stakers',
                        'average_stake_formatted',
                        'average_lock_duration',
                        'average_apy',
                        'deposit_volume_formatted',
                        'withdrawal_volume_formatted',
                        'utilization_rate',
                        'pool_growth_percentage',
                        'timestamp',
                    ],
                ],
            ]);

        $responseSingle = $this->getJson('/api/v1/analytics/pools/pool-1');
        $responseSingle->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'pool_id',
                    'tvl_raw',
                    'tvl_formatted',
                    'active_stakers',
                    'average_stake_formatted',
                    'average_lock_duration',
                    'average_apy',
                    'deposit_volume_formatted',
                    'withdrawal_volume_formatted',
                    'utilization_rate',
                    'pool_growth_percentage',
                    'timestamp',
                ],
            ]);
    }
}
