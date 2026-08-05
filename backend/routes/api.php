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
use App\Http\Controllers\Api\V1\Monitoring\AlertMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\CacheMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\ExportMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\HistoricalMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\IndexerHistoryMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\MonitoringDashboardController;
use App\Http\Controllers\Api\V1\Monitoring\MonitoringHealthController;
use App\Http\Controllers\Api\V1\Monitoring\PerformanceMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\QueueMonitoringController;
use App\Http\Controllers\Api\V1\Monitoring\RpcMetricsMonitoringController;
use App\Http\Controllers\Api\V1\NetworkController;
use App\Http\Controllers\Api\V1\PoolAnalyticsController;
use App\Http\Controllers\Api\V1\PoolController;
use App\Http\Controllers\Api\V1\ProtocolAnalyticsController;
use App\Http\Controllers\Api\V1\Research\EventResearchController;
use App\Http\Controllers\Api\V1\Research\FeatureResearchController;
use App\Http\Controllers\Api\V1\Research\PoolResearchController;
use App\Http\Controllers\Api\V1\Research\ResearchDashboardController;
use App\Http\Controllers\Api\V1\Research\ResearchExportController;
use App\Http\Controllers\Api\V1\Research\StatisticResearchController;
use App\Http\Controllers\Api\V1\Research\WalletResearchController;
use App\Http\Controllers\Api\V1\RewardAnalyticsController;
use App\Http\Controllers\Api\V1\RewardController;
use App\Http\Controllers\Api\V1\Security\ApiKeyManagementController;
use App\Http\Controllers\Api\V1\Security\AuthController;
use App\Http\Controllers\Api\V1\Security\SecurityMonitoringController;
use App\Http\Controllers\Api\V1\Security\SiweAuthController;
use App\Http\Controllers\Api\V1\StakeController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\TVLAnalyticsController;
use App\Http\Controllers\Api\V1\WalletAnalyticsController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Phase B6 Authentication Endpoints
    Route::prefix('auth')->group(function (): void {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware(JwtAuthMiddleware::class);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me'])->middleware(JwtAuthMiddleware::class);

        // SIWE Wallet Auth
        Route::get('/nonce', [SiweAuthController::class, 'nonce']);
        Route::post('/verify', [SiweAuthController::class, 'verify']);
    });

    // Public / Core Endpoints
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

    // Phase B5 Institutional Monitoring & Operations Platform
    Route::prefix('monitoring')->group(function (): void {
        Route::get('/dashboard', [MonitoringDashboardController::class, 'index']);
        Route::get('/health', [MonitoringHealthController::class, 'show']);
        Route::get('/queues', [QueueMonitoringController::class, 'index']);
        Route::get('/cache', [CacheMonitoringController::class, 'index']);
        Route::get('/indexer/history', [IndexerHistoryMonitoringController::class, 'index']);
        Route::get('/rpc', [RpcMetricsMonitoringController::class, 'index']);

        Route::get('/alerts', [AlertMonitoringController::class, 'index']);
        Route::post('/alerts/{id}/acknowledge', [AlertMonitoringController::class, 'acknowledge']);
        Route::get('/alerts/rules', [AlertMonitoringController::class, 'rules']);

        Route::get('/history', [HistoricalMonitoringController::class, 'index']);
        Route::get('/export/events', [ExportMonitoringController::class, 'events']);
        Route::get('/export/metrics', [ExportMonitoringController::class, 'metrics']);
        Route::get('/performance', [PerformanceMonitoringController::class, 'index']);
    });

    // Phase B6 Security & API Gateway Endpoints
    Route::prefix('security')->group(function (): void {
        Route::get('/dashboard', [SecurityMonitoringController::class, 'dashboard']);
        Route::get('/audit', [SecurityMonitoringController::class, 'audit']);
        Route::get('/rate-limit', [SecurityMonitoringController::class, 'rateLimit']);
        Route::get('/sessions', [SecurityMonitoringController::class, 'sessions']);

        Route::get('/api-keys', [ApiKeyManagementController::class, 'index']);
        Route::post('/api-keys', [ApiKeyManagementController::class, 'store'])->middleware(JwtAuthMiddleware::class);
        Route::delete('/api-keys/{id}', [ApiKeyManagementController::class, 'destroy'])->middleware(JwtAuthMiddleware::class);
    });

    // Phase B7 Data Intelligence & Research Platform Endpoints
    Route::prefix('research')->group(function (): void {
        Route::get('/dashboard', [ResearchDashboardController::class, 'index']);
        Route::get('/wallets', [WalletResearchController::class, 'index']);
        Route::get('/pools', [PoolResearchController::class, 'index']);
        Route::get('/features', [FeatureResearchController::class, 'index']);
        Route::get('/events', [EventResearchController::class, 'index']);
        Route::get('/statistics', [StatisticResearchController::class, 'index']);
        Route::get('/export/{type}', [ResearchExportController::class, 'export']);
    });
});
