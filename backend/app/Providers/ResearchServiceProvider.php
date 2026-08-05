<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Research\BenchmarkService;
use App\Services\Research\DataQualityEngine;
use App\Services\Research\FeatureStoreService;
use App\Services\Research\ResearchDatasetEngine;
use App\Services\Research\ResearchExportService;
use App\Services\Research\ResearchTimeSeriesBuilder;
use Illuminate\Support\ServiceProvider;

class ResearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DataQualityEngine::class);
        $this->app->singleton(FeatureStoreService::class);
        $this->app->singleton(ResearchDatasetEngine::class);
        $this->app->singleton(ResearchTimeSeriesBuilder::class);
        $this->app->singleton(BenchmarkService::class);
        $this->app->singleton(ResearchExportService::class);
    }

    public function boot(): void
    {
        //
    }
}
