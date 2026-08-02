<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoolAnalyticsResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;
use App\Services\Analytics\DTO\PoolAnalyticsDTO;

class PoolAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function index()
    {
        $pools = $this->analyticsService->getPoolAnalytics();

        return PoolAnalyticsResource::collection(is_array($pools) ? $pools : [$pools]);
    }

    public function show(string $id): PoolAnalyticsResource
    {
        /** @var PoolAnalyticsDTO $pool */
        $pool = $this->analyticsService->getPoolAnalytics($id);

        return new PoolAnalyticsResource($pool);
    }
}
