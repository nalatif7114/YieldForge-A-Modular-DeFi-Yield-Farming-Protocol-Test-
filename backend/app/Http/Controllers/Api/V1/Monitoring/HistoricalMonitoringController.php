<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\MonitoringSnapshot;
use Illuminate\Http\JsonResponse;

class HistoricalMonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        $snapshots = MonitoringSnapshot::query()
            ->orderByDesc('timestamp')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $snapshots,
        ]);
    }
}
