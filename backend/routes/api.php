<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AnalyticsHealthController;
use App\Http\Controllers\Api\V1\AnalyticsOverviewController;
use App\Http\Controllers\Api\V1\APYAnalyticsController;
use App\Http\Controllers\Api\V1\ChartAnalyticsController;
use App\Http\Controllers\Api\V1\ContractController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\HistoryAnalyticsController;
use App\Http\Controllers\Api\V1\IndexerController;
use App\Http\Controllers\Api\V1\NetworkController;
use App\Http\Controllers\Api\V1\PoolAnalyticsController;
use App\Http\Controllers\Api\V1\PoolController;
use App\Http\Controllers\Api\V1\ProtocolAnalyticsController;
use App\Http\Controllers\Api\V1\RewardAnalyticsController;
use App\Http\Controllers\Api\V1\RewardController;
use App\Http\Controllers\Api\V1\StakeController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\TVLAnalyticsController;
use App\Http\Controllers\Api\V1\WalletAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/network', [NetworkController::class, 'index']);
    Route::get('/contracts', [ContractController::class, 'index']);
    Route::get('/pools', [PoolController::class, 'index']);
    Route::get('/stakes/{wallet}', [StakeController::class, 'show']);
    Route::get('/rewards/{wallet}', [RewardController::class, 'show']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/health', [HealthController::class, 'show']);

    Route::get('/indexer', [IndexerController::class, 'index']);
    Route::get('/indexer/metrics', [IndexerController::class, 'metrics']);
    Route::get('/stats', [StatsController::class, 'index']);

    // Phase B4 Protocol Analytics Engine
    Route::prefix('analytics')->group(function (): void {
        Route::get('/overview', [AnalyticsOverviewController::class, 'index']);
        Route::get('/tvl', [TVLAnalyticsController::class, 'index']);
        Route::get('/apy', [APYAnalyticsController::class, 'index']);
        Route::get('/protocol', [ProtocolAnalyticsController::class, 'index']);
        Route::get('/pools', [PoolAnalyticsController::class, 'index']);
        Route::get('/pools/{id}', [PoolAnalyticsController::class, 'show']);
        Route::get('/wallet/{address}', [WalletAnalyticsController::class, 'show']);
        Route::get('/rewards', [RewardAnalyticsController::class, 'index']);
        Route::get('/history', [HistoryAnalyticsController::class, 'index']);

        Route::prefix('charts')->group(function (): void {
            Route::get('/tvl', [ChartAnalyticsController::class, 'tvl']);
            Route::get('/apy', [ChartAnalyticsController::class, 'apy']);
            Route::get('/rewards', [ChartAnalyticsController::class, 'rewards']);
            Route::get('/transactions', [ChartAnalyticsController::class, 'transactions']);
        });

        Route::get('/health', [AnalyticsHealthController::class, 'index']);
    });
});
