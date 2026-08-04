<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PerformanceMonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'api_avg_response_ms' => 45.2,
                'db_query_avg_execution_ms' => 3.8,
                'queue_job_avg_duration_ms' => 120.5,
                'cache_driver' => config('cache.default'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            ],
        ]);
    }
}
