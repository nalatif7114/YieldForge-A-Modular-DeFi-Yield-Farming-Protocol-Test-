<?php

declare(strict_types=1);

namespace App\Services\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Indexer\DTO\IndexerContext;
use App\Services\Indexer\DTO\IndexerStateDTO;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class IndexerHealthService
{
    public function __construct(
        private readonly NetworkServiceInterface $networkService,
        private readonly BlockCursor $blockCursor,
        private readonly ConfigRepository $config
    ) {}

    public function getHealth(?IndexerContext $context = null): IndexerStateDTO
    {
        $context ??= new IndexerContext(
            chainId: (int) $this->config->get('blockchain.chain_id', 11155111),
            network: (string) $this->config->get('blockchain.network_name', 'sepolia'),
            rpcEndpoint: (string) $this->config->get('blockchain.rpc_url')
        );

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
                network: $context->network,
                latestBlock: $onChainLatest,
                indexedBlock: $indexedLatest,
                lag: $lag,
                events: $totalEvents,
                status: $status,
                lastSync: now()->toIso8601String(),
                projectionVersion: (string) $this->config->get('blockchain.projection_version', '1.0.0')
            );
        } catch (Throwable) {
            return new IndexerStateDTO(
                network: $context->network,
                latestBlock: 0,
                indexedBlock: 0,
                lag: 0,
                events: 0,
                status: 'error',
                lastSync: now()->toIso8601String(),
                projectionVersion: (string) $this->config->get('blockchain.projection_version', '1.0.0')
            );
        }
    }
}
