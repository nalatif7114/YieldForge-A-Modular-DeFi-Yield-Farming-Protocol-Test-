<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(
        private readonly NetworkServiceInterface $networkService
    ) {}

    public function show(): JsonResponse
    {
        $isHealthy = $this->networkService->isHealthy();
        $networkInfo = $this->networkService->getNetworkInfo();

        $statusCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status' => $isHealthy ? 'ok' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'api' => 'up',
                'blockchain' => [
                    'connected' => $networkInfo->isConnected,
                    'chain_id' => $networkInfo->chainId,
                    'block_number' => $networkInfo->blockNumber,
                    'latency_ms' => $networkInfo->latencyMs,
                ],
            ],
        ], $statusCode);
    }
}
