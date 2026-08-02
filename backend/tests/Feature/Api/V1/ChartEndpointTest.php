<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_tvl_chart_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/charts/tvl?window=30d');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['timestamp', 'value'],
                ],
            ]);
    }

    public function test_get_apy_chart_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/charts/apy?window=30d');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['timestamp', 'value'],
                ],
            ]);
    }

    public function test_get_rewards_chart_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/charts/rewards?window=30d');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['timestamp', 'value'],
                ],
            ]);
    }

    public function test_get_transactions_chart_endpoint(): void
    {
        $response = $this->getJson('/api/v1/analytics/charts/transactions?window=30d');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['timestamp', 'value'],
                ],
            ]);
    }
}
