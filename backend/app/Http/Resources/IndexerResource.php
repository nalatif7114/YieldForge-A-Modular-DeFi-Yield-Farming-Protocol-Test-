<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Indexer\DTO\IndexerStateDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property IndexerStateDTO $resource
 */
class IndexerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
