<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\RpcMetricsMonitorService;
use Illuminate\Http\JsonResponse;

class RpcMetricsMonitoringController extends Controller
{
    public function index(RpcMetricsMonitorService $rpcMonitorService): JsonResponse
    {
        $metrics = $rpcMonitorService->getMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics->toArray(),
        ]);
    }
}
