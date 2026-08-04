<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\BlockchainServiceProvider::class,
    App\Providers\IndexerServiceProvider::class,
    App\Providers\AnalyticsServiceProvider::class,
    App\Providers\MonitoringServiceProvider::class,
    App\Providers\SecurityServiceProvider::class,
];
