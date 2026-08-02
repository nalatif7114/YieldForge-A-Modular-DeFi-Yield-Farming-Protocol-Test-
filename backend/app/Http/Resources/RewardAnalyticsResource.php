<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\RewardAnalyticsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property RewardAnalyticsResource $resource
 */
class RewardAnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource instanceof RewardAnalyticsDTO
            ? $this->resource->toArray()
            : (array) $this->resource;
    }
}
