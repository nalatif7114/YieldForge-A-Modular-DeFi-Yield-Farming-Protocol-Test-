<?php

declare(strict_types=1);

namespace Tests\Feature\Research;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_research_dashboard_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/dashboard');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['datasets_count', 'feature_sets_count', 'overall_quality_score', 'dataset_health_status']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.overall_quality_score', 100)
            ->assertJsonPath('data.dataset_health_status', 'passed');
    }

    public function test_research_wallets_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/wallets');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['total_wallets', 'wallets']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.total_wallets', 0);
    }

    public function test_research_pools_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/pools');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['active_pools_count', 'pool_features']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.active_pools_count', 0);
    }

    public function test_research_features_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/features');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['feature_sets']])
            ->assertJsonPath('status', 'success');
    }

    public function test_research_events_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/events');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['total_events', 'events']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.total_events', 0);
    }

    public function test_research_statistics_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/statistics');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['benchmarks', 'time_series']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.benchmarks.time_window', '30d');
    }
}
