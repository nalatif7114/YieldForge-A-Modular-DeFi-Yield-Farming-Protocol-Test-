<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ProtocolStatistic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProtocolStatistic $resource
 */
class StatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_value_locked_raw' => $this->resource->total_value_locked_raw ?? '0',
            'total_value_locked_formatted' => $this->resource->total_value_locked_formatted ?? '0',
            'total_stakers_count' => (int) ($this->resource->total_stakers_count ?? 0),
            'total_events_processed' => (int) ($this->resource->total_events_processed ?? 0),
            'total_tokens_minted_raw' => $this->resource->total_tokens_minted_raw ?? '0',
            'total_tokens_burned_raw' => $this->resource->total_tokens_burned_raw ?? '0',
            'latest_indexed_block' => (int) ($this->resource->latest_indexed_block ?? 0),
        ];
    }
}
