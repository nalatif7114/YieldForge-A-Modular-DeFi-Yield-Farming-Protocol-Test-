<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Analytics\DTO\WalletAnalyticsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property WalletAnalyticsDTO $resource
 */
class WalletAnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
