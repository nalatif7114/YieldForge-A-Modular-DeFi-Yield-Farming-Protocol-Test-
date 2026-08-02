<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TVLHistoryResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;
use Illuminate\Http\Request;

class HistoryAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index(Request $request): TVLHistoryResource
    {
        $window = (string) $request->query('window', '30d');

        return new TVLHistoryResource($this->analyticsService->getTvlHistory($window));
    }
}
