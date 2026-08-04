<?php

declare(strict_types=1);

namespace App\Services\Indexer\DTO;

readonly class IndexerStateDTO
{
    public function __construct(
        public bool $rpcConnected,
        public float $rpcLatencyMs,
        public int $chainId,
        public int $expectedChainId,
        public int $latestRpcBlock,
        public int $latestIndexedBlock,
        public int $syncLag,
        public bool $contractsLoaded,
        public bool $abiLoaded,
        public ?string $lastRpcError,
        public float $lastSyncDurationMs,
        public string $network,
        public int $events,
        public string $status,
        public string $lastSync,
        public string $projectionVersion = '1.0.0'
    ) {}

    public function toArray(): array
    {
        return [
            'rpc_connected' => $this->rpcConnected,
            'rpc_latency_ms' => $this->rpcLatencyMs,
            'chain_id' => $this->chainId,
            'expected_chain_id' => $this->expectedChainId,
            'latest_rpc_block' => $this->latestRpcBlock,
            'latest_indexed_block' => $this->latestIndexedBlock,
            'sync_lag' => $this->syncLag,
            'contracts_loaded' => $this->contractsLoaded,
            'abi_loaded' => $this->abiLoaded,
            'last_rpc_error' => $this->lastRpcError,
            'last_sync_duration_ms' => $this->lastSyncDurationMs,
            'network' => $this->network,
            'latest_block' => $this->latestRpcBlock,
            'indexed_block' => $this->latestIndexedBlock,
            'lag' => $this->syncLag,
            'events' => $this->events,
            'status' => $this->status,
            'last_sync' => $this->lastSync,
            'projection_version' => $this->projectionVersion,
        ];
    }
}
