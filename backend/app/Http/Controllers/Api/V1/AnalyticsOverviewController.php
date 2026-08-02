<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnalyticsOverviewResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;

class AnalyticsOverviewController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index(): AnalyticsOverviewResource
    {
        return new AnalyticsOverviewResource($this->analyticsService->getOverview());
    }
}
