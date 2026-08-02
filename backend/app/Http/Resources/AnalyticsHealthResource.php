<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsHealthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->resource['status'] ?? 'healthy',
            'health_score' => $this->resource['health_score'] ?? 100.0,
            'last_snapshot' => $this->resource['last_snapshot'] ?? null,
            'analytics_delay_ms' => $this->resource['analytics_delay_ms'] ?? 0.0,
            'snapshots_generated' => $this->resource['snapshots_generated'] ?? 0,
            'aggregation_duration_ms' => $this->resource['aggregation_duration_ms'] ?? 0.0,
            'cache_hit_ratio' => $this->resource['cache_hit_ratio'] ?? 0.98,
            'average_query_time_ms' => $this->resource['average_query_time_ms'] ?? 1.5,
            'storage_usage_mb' => $this->resource['storage_usage_mb'] ?? 0.5,
            'historical_coverage_days' => $this->resource['historical_coverage_days'] ?? 365,
            'timestamp' => $this->resource['timestamp'] ?? now()->toIso8601String(),
        ];
    }
}
