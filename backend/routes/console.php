<?php

declare(strict_types=1);

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
