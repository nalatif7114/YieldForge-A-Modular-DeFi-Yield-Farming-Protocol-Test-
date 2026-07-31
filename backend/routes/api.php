<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ContractController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\NetworkController;
use App\Http\Controllers\Api\V1\PoolController;
use App\Http\Controllers\Api\V1\RewardController;
use App\Http\Controllers\Api\V1\StakeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/network', [NetworkController::class, 'index']);
    Route::get('/contracts', [ContractController::class, 'index']);
    Route::get('/pools', [PoolController::class, 'index']);
    Route::get('/stakes/{wallet}', [StakeController::class, 'show']);
    Route::get('/rewards/{wallet}', [RewardController::class, 'show']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/health', [HealthController::class, 'show']);
});
