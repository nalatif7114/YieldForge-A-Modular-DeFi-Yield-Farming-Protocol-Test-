<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\QueueMonitorService;
use Illuminate\Http\JsonResponse;

class QueueMonitoringController extends Controller
{
    public function index(QueueMonitorService $queueMonitorService): JsonResponse
    {
        $metrics = $queueMonitorService->getMetrics();

        return response()->json([
            'status' => 'success',
            'data' => $metrics->toArray(),
        ]);
    }
}
