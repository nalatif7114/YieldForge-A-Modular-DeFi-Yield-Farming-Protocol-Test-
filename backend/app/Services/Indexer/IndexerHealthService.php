<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Blockchain\Contracts\AbiLoaderInterface;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Indexer\DTO\IndexerContext;
use App\Services\Indexer\DTO\IndexerStateDTO;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class IndexerHealthService
{
    public function __construct(
        private readonly NetworkServiceInterface $networkService,
        private readonly BlockCursor $blockCursor,
        private readonly AbiLoaderInterface $abiLoader,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config
    ) {}

    public function getHealth(?IndexerContext $context = null): IndexerStateDTO
    {
        $context ??= new IndexerContext(
            chainId: (int) $this->config->get('blockchain.chain_id', 11155111),
            network: (string) $this->config->get('blockchain.network_name', 'sepolia'),
            rpcEndpoint: (string) $this->config->get('blockchain.rpc_url')
        );

        $expectedChainId = (int) $this->config->get('blockchain.chain_id', 11155111);
        $lastSyncDurationMs = (float) $this->cache->get('indexer_metrics:last_sync_duration', 0.0);

        // Check ABI loading
        $abiLoaded = true;
        try {
            $this->abiLoader->getAbi('YieldForgeToken');
            $this->abiLoader->getAbi('YieldForgeStaking');
        } catch (Throwable) {
            $abiLoaded = false;
        }

        // Check contracts config
        $tokenAddr = (string) $this->config->get('blockchain.contracts.token.address');
        $stakingAddr = (string) $this->config->get('blockchain.contracts.staking.address');
        $contractsLoaded = $tokenAddr !== '' && $stakingAddr !== '';

        try {
            $networkInfo = $this->networkService->getNetworkInfo();
            $onChainLatest = $networkInfo->blockNumber;
            $indexedLatest = $this->blockCursor->getLatestIndexedBlock($context);
            $totalEvents = BlockchainEvent::count();

            $lag = max(0, $onChainLatest - $indexedLatest);

            $status = match (true) {
                !$networkInfo->isConnected => 'disconnected',
                $lag <= 5 => 'healthy',
                $lag <= 50 => 'syncing',
                default => 'degraded',
            };

            return new IndexerStateDTO(
                rpcConnected: $networkInfo->isConnected,
                rpcLatencyMs: $networkInfo->latencyMs,
                chainId: $networkInfo->chainId,
                expectedChainId: $expectedChainId,
                latestRpcBlock: $onChainLatest,
                latestIndexedBlock: $indexedLatest,
                syncLag: $lag,
                contractsLoaded: $contractsLoaded,
                abiLoaded: $abiLoaded,
                lastRpcError: $networkInfo->isConnected ? null : 'RPC connection failed',
                lastSyncDurationMs: $lastSyncDurationMs,
                network: $context->network,
                events: $totalEvents,
                status: $status,
                lastSync: now()->toIso8601String(),
                projectionVersion: (string) $this->config->get('blockchain.projection_version', '1.0.0')
            );
        } catch (Throwable $e) {
            return new IndexerStateDTO(
                rpcConnected: false,
                rpcLatencyMs: 0.0,
                chainId: $expectedChainId,
                expectedChainId: $expectedChainId,
                latestRpcBlock: 0,
                latestIndexedBlock: 0,
                syncLag: 0,
                contractsLoaded: $contractsLoaded,
                abiLoaded: $abiLoaded,
                lastRpcError: $e->getMessage(),
                lastSyncDurationMs: $lastSyncDurationMs,
                network: $context->network,
                events: 0,
                status: 'error',
                lastSync: now()->toIso8601String(),
                projectionVersion: (string) $this->config->get('blockchain.projection_version', '1.0.0')
            );
        }
    }
}
