<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Analytics\AnalyticsCache;
use App\Services\Analytics\AnalyticsEngine;
use App\Services\Analytics\APYCalculator;
use App\Services\Analytics\BenchmarkCalculator;
use App\Services\Analytics\ChartDataBuilder;
use App\Services\Analytics\Contracts\AnalyticsCacheInterface;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;
use App\Services\Analytics\Contracts\BenchmarkCalculatorInterface;
use App\Services\Analytics\Contracts\HistoricalDataInterface;
use App\Services\Analytics\Contracts\KpiServiceInterface;
use App\Services\Analytics\Contracts\MetricsAggregatorInterface;
use App\Services\Analytics\GrowthCalculator;
use App\Services\Analytics\HealthScoreCalculator;
use App\Services\Analytics\KpiService;
use App\Services\Analytics\MetricsAggregator;
use App\Services\Analytics\PortfolioAnalyzer;
use App\Services\Analytics\RewardAnalyzer;
use App\Services\Analytics\SnapshotBuilder;
use App\Services\Analytics\TimeSeriesBuilder;
use App\Services\Analytics\TVLCalculator;
use App\Services\Analytics\YieldCalculator;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TVLCalculator::class);
        $this->app->singleton(APYCalculator::class);
        $this->app->singleton(YieldCalculator::class);
        $this->app->singleton(GrowthCalculator::class);
        $this->app->singleton(HealthScoreCalculator::class);
        $this->app->singleton(PortfolioAnalyzer::class);
        $this->app->singleton(RewardAnalyzer::class);
        $this->app->singleton(SnapshotBuilder::class);
        $this->app->singleton(TimeSeriesBuilder::class);

        $this->app->singleton(AnalyticsCacheInterface::class, AnalyticsCache::class);
        $this->app->singleton(HistoricalDataInterface::class, ChartDataBuilder::class);
        $this->app->singleton(KpiServiceInterface::class, KpiService::class);
        $this->app->singleton(BenchmarkCalculatorInterface::class, BenchmarkCalculator::class);
        $this->app->singleton(MetricsAggregatorInterface::class, MetricsAggregator::class);
        $this->app->singleton(AnalyticsServiceInterface::class, AnalyticsEngine::class);
    }

    public function boot(): void
    {
        //
    }
}
