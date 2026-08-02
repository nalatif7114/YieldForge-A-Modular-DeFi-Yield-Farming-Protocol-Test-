<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletAnalyticsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_wallet_analytics_endpoint(): void
    {
        $wallet = '0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266';
        $response = $this->getJson("/api/v1/analytics/wallet/{$wallet}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'wallet',
                    'tvl_raw',
                    'tvl_formatted',
                    'rewards_raw',
                    'rewards_formatted',
                    'pending_rewards_raw',
                    'pending_rewards_formatted',
                    'roi_percentage',
                    'apy_percentage',
                    'compounded_yield_formatted',
                    'pool_allocation',
                    'risk_metrics' => [
                        'diversification_score',
                        'concentration_risk',
                        'largest_pool_exposure',
                        'impermanent_risk_estimate',
                        'reward_dependency_ratio',
                    ],
                    'last_active_at',
                    'timestamp',
                ],
            ]);
    }
}
