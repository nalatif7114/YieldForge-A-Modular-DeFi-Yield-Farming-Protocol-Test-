<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\CacheMonitorService;
use Illuminate\Http\JsonResponse;

class CacheMonitoringController extends Controller
{
    public function index(CacheMonitorService $cacheMonitorService): JsonResponse
    {
        $metrics = $cacheMonitorService->getMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics->toArray(),
        ]);
    }
}
