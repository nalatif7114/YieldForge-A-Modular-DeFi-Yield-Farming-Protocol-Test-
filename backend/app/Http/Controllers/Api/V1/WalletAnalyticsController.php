<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletAnalyticsResource;
use App\Services\Analytics\Contracts\AnalyticsServiceInterface;

class WalletAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsServiceInterface $analyticsService
    ) {}

    public function show(string $address): WalletAnalyticsResource
    {
        return new WalletAnalyticsResource($this->analyticsService->getWalletAnalytics($address));
    }
}
