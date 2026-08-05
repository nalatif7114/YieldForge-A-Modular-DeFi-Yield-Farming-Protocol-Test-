<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Services\Research\BenchmarkService;
use App\Services\Research\ResearchTimeSeriesBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticResearchController extends Controller
{
    public function index(
        Request $request,
        BenchmarkService $benchmarkService,
        ResearchTimeSeriesBuilder $seriesBuilder
    ): JsonResponse {
        $window = (string) $request->query('window', '30d');
        $interval = (string) $request->query('interval', 'daily');

        $benchmarks = $benchmarkService->getBenchmarks($window);
        $series = $seriesBuilder->buildSeries($interval, 30);

        return response()->json([
            'status' => 'success',
            'data' => [
                'benchmarks' => $benchmarks,
                'time_series' => $series,
            ],
        ]);
    }
}
