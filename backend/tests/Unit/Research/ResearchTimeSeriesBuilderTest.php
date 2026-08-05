<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use App\Models\DailyStatistic;
use App\Models\HourlyStatistic;
use App\Services\Research\ResearchTimeSeriesBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchTimeSeriesBuilderTest extends TestCase
{
    use RefreshDatabase;

    private ResearchTimeSeriesBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->app->make(ResearchTimeSeriesBuilder::class);
    }

    public function test_build_hourly_series(): void
    {
        HourlyStatistic::create([
            'timestamp' => now()->startOfHour(),
            'tvl_formatted' => '100.0',
            'volume_formatted' => '10.0',
            'active_users' => 1,
            'tx_count' => 1,
        ]);

        $series = $this->builder->buildSeries('hourly', 10);
        $this->assertCount(1, $series);
        $this->assertEquals('100.0', $series->first()->tvl_formatted);
    }

    public function test_build_daily_series(): void
    {
        DailyStatistic::create([
            'timestamp' => now()->startOfDay(),
            'tvl_formatted' => '500.0',
            'volume_formatted' => '50.0',
            'active_users' => 5,
            'tx_count' => 10,
        ]);

        $series = $this->builder->buildSeries('daily', 10);
        $this->assertCount(1, $series);
        $this->assertEquals('500.0', $series->first()->tvl_formatted);
    }
}
