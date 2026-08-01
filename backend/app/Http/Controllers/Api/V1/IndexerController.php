<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndexerMetricsResource;
use App\Http\Resources\IndexerResource;
use App\Services\Indexer\IndexerHealthService;
use App\Services\Indexer\IndexerMetricsService;

class IndexerController extends Controller
{
    public function __construct(
        private readonly IndexerHealthService $healthService,
        private readonly IndexerMetricsService $metricsService
    ) {}

    public function index(): IndexerResource
    {
        return new IndexerResource($this->healthService->getHealth());
    }

    public function metrics(): IndexerMetricsResource
    {
        return new IndexerMetricsResource($this->metricsService->getMetrics());
    }
}
