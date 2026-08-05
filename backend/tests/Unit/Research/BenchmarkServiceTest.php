<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use App\Models\ProtocolStatistic;
use App\Services\Research\BenchmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BenchmarkServiceTest extends TestCase
{
    use RefreshDatabase;

    private BenchmarkService $benchmarkService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->benchmarkService = $this->app->make(BenchmarkService::class);
    }

    public function test_get_benchmarks_returns_metrics_structure(): void
    {
        ProtocolStatistic::create([
            'total_value_locked_raw' => '1000000000000000000000',
            'total_value_locked_formatted' => '1000.0',
            'active_stakers_count' => 1,
            'total_staked_events' => 1,
            'total_withdrawn_events' => 0,
            'total_reward_events' => 0,
            'active_pools_count' => 1,
        ]);

        $res = $this->benchmarkService->getBenchmarks('30d');

        $this->assertIsArray($res);
        $this->assertEquals('30d', $res['time_window']);
        $this->assertEquals('1000.0', $res['total_value_locked']);
        $this->assertArrayHasKey('tvl_growth_percentage', $res);
    }
}
