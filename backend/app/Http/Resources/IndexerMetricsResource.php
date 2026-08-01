<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Indexer\DTO\IndexerMetricsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property IndexerMetricsDTO $resource
 */
class IndexerMetricsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
