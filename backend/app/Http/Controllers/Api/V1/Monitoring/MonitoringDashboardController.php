<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\MonitoringDashboardService;
use Illuminate\Http\JsonResponse;

class MonitoringDashboardController extends Controller
{
    public function index(MonitoringDashboardService $dashboardService): JsonResponse
    {
        $dashboard = $dashboardService->getDashboard();

        return response()->json([
            'status' => 'success',
            'data' => $dashboard->toArray(),
        ]);
    }
}
