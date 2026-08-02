<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\ProtocolAnalyticsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProtocolAnalyticsDTO $resource
 */
class ProtocolAnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
