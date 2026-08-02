<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\PoolAnalyticsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property PoolAnalyticsDTO $resource
 */
class PoolAnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource instanceof PoolAnalyticsDTO
            ? $this->resource->toArray()
            : (array) $this->resource;
    }
}
