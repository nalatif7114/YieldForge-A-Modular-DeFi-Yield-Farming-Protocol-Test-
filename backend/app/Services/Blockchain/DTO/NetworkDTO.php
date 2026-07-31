<?php

declare(strict_types=1);

namespace App\Services\Blockchain\DTO;

readonly class NetworkDTO
{
    public function __construct(
        public int $chainId,
        public string $networkName,
        public int $blockNumber,
        public string $rpcUrl,
        public bool $isConnected,
        public float $latencyMs
    ) {}

    /**
     * Convert DTO to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'chain_id' => $this->chainId,
            'network_name' => $this->networkName,
            'block_number' => $this->blockNumber,
            'rpc_url' => $this->rpcUrl,
            'is_connected' => $this->isConnected,
            'latency_ms' => $this->latencyMs,
        ];
    }
}
