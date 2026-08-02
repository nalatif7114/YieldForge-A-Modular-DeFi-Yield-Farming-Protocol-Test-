<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\ChartPointDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            return array_map(function ($item) {
                return $item instanceof ChartPointDTO ? $item->toArray() : (array) $item;
            }, $this->resource);
        }

        return $this->resource instanceof ChartPointDTO ? $this->resource->toArray() : (array) $this->resource;
    }
}
