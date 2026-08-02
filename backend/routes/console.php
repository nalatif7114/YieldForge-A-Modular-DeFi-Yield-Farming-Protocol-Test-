<?php

declare(strict_types=1);

use App\Jobs\AggregateDailyStatisticsJob;
use App\Jobs\AggregateHourlyStatisticsJob;
use App\Jobs\AnalyticsSnapshotJob;
use App\Jobs\CleanupHistoricalDataJob;
use App\Jobs\IntegrityVerificationJob;
use App\Jobs\RefreshProtocolStatisticsJob;
use App\Jobs\SyncLatestBlockJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncLatestBlockJob())->everyTenSeconds();
Schedule::job(new RefreshProtocolStatisticsJob())->hourly();
Schedule::job(new IntegrityVerificationJob())->dailyAt('03:00');

Schedule::job(new AnalyticsSnapshotJob())->everyFiveMinutes();
Schedule::job(new AggregateHourlyStatisticsJob())->hourly();
Schedule::job(new AggregateDailyStatisticsJob())->daily();
Schedule::job(new CleanupHistoricalDataJob())->weekly();
