<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\MonitoringDashboardService;
use Illuminate\Http\JsonResponse;

class MonitoringHealthController extends Controller
{
    public function show(MonitoringDashboardService $dashboardService): JsonResponse
    {
        $dashboard = $dashboardService->getDashboard();

        return response()->json([
            'status' => 'success',
            'data' => [
                'health_score' => $dashboard->healthScore,
                'overall_status' => $dashboard->overallStatus,
                'components' => [
                    'indexer' => $dashboard->indexer['status'] ?? 'unknown',
                    'rpc' => $dashboard->rpc->status,
                    'queue' => $dashboard->queue->status,
                    'cache' => $dashboard->cache->status,
                ],
                'active_alerts_count' => $dashboard->alertsSummary['total_active'] ?? 0,
                'timestamp' => $dashboard->timestamp,
            ],
        ]);
    }
}
