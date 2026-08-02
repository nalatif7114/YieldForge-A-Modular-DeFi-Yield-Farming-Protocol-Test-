<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChartResource;
use App\Services\Analytics\ChartDataBuilder;
use Illuminate\Http\Request;

class ChartAnalyticsController extends Controller
{
    public function __construct(
        private readonly ChartDataBuilder $chartDataBuilder
    ) {}

    public function tvl(Request $request): ChartResource
    {
        $window = (string) $request->query('window', '30d');

        return new ChartResource($this->chartDataBuilder->getChartData('tvl', $window));
    }

    public function apy(Request $request): ChartResource
    {
        $window = (string) $request->query('window', '30d');

        return new ChartResource($this->chartDataBuilder->getChartData('apy', $window));
    }

    public function rewards(Request $request): ChartResource
    {
        $window = (string) $request->query('window', '30d');

        return new ChartResource($this->chartDataBuilder->getChartData('rewards', $window));
    }

    public function transactions(Request $request): ChartResource
    {
        $window = (string) $request->query('window', '30d');

        return new ChartResource($this->chartDataBuilder->getChartData('transactions', $window));
    }
}
