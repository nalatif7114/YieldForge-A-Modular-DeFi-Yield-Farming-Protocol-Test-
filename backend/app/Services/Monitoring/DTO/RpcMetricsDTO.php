<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTO;

readonly class RpcMetricsDTO
{
    public function __construct(
        public string $endpoint,
        public int $chainId,
        public string $networkName,
        public float $latencyMs,
        public bool $isConnected,
        public float $successRatePercentage,
        public int $totalRequestsCount,
        public int $errorCount,
        public string $status
    ) {}

    public function toArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'chain_id' => $this->chainId,
            'network_name' => $this->networkName,
            'latency_ms' => round($this->latencyMs, 2),
            'is_connected' => $this->isConnected,
            'success_rate_percentage' => round($this->successRatePercentage, 2),
            'total_requests_count' => $this->totalRequestsCount,
            'error_count' => $this->errorCount,
            'status' => $this->status,
        ];
    }
}
