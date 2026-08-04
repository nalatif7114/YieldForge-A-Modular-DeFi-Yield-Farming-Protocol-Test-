<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\HourlyStatistic;
use App\Models\IndexedBlock;
use Illuminate\Http\JsonResponse;

class IndexerHistoryMonitoringController extends Controller
{
    public function index(): JsonResponse
    {
        $recentBlocks = IndexedBlock::query()
            ->orderByDesc('block_number')
            ->limit(20)
            ->get();

        $hourlyStats = HourlyStatistic::query()
            ->orderByDesc('timestamp')
            ->limit(24)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'recent_blocks' => $recentBlocks,
                'hourly_statistics' => $hourlyStats,
            ],
        ]);
    }
}
