<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Blockchain\DTO\PoolDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property PoolDTO $resource
 */
class PoolResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
