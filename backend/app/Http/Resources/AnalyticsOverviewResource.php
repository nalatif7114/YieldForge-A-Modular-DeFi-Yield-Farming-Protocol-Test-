<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'kpis' => $this->resource['kpis'] ?? [],
            'benchmarks' => $this->resource['benchmarks'] ?? [],
            'health_score' => $this->resource['health_score'] ?? 100.0,
            'timestamp' => $this->resource['timestamp'] ?? now()->toIso8601String(),
        ];
    }
}
