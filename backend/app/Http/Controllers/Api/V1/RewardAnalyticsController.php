<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RewardAnalyticsResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;

class RewardAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index(): RewardAnalyticsResource
    {
        return new RewardAnalyticsResource($this->analyticsService->getRewardAnalytics());
    }
}
