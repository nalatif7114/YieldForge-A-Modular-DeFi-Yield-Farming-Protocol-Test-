<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\APYHistoryResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;
use Illuminate\Http\Request;

class APYAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index(Request $request): APYHistoryResource
    {
        $window = (string) $request->query('window', '30d');

        return new APYHistoryResource($this->analyticsService->getApyHistory($window));
    }
}
