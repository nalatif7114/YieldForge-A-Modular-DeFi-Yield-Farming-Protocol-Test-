<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProtocolAnalyticsResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;

class ProtocolAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index(): ProtocolAnalyticsResource
    {
        return new ProtocolAnalyticsResource($this->analyticsService->getProtocolAnalytics());
    }
}
