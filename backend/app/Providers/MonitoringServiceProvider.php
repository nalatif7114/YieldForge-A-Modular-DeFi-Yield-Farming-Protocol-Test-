<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Monitoring\AlertEngineService;
use App\Services\Monitoring\CacheMonitorService;
use App\Services\Monitoring\ExportService;
use App\Services\Monitoring\MonitoringDashboardService;
use App\Services\Monitoring\QueueMonitorService;
use App\Services\Monitoring\RpcMetricsMonitorService;
use Illuminate\Support\ServiceProvider;

class MonitoringServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QueueMonitorService::class);
        $this->app->singleton(CacheMonitorService::class);
        $this->app->singleton(RpcMetricsMonitorService::class);
        $this->app->singleton(AlertEngineService::class);
        $this->app->singleton(ExportService::class);
        $this->app->singleton(MonitoringDashboardService::class);
    }

    public function boot(): void
    {
        //
    }
}
