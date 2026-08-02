<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\APYHistoryDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property APYHistoryDTO $resource
 */
class APYHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
