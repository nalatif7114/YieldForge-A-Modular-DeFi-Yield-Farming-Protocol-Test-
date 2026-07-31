<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Blockchain\DTO\RewardDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property RewardDTO $resource
 */
class RewardResource extends JsonResource
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
