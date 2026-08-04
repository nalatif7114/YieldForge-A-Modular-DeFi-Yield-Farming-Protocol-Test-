<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\DTO\NetworkDTO;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class NetworkService implements NetworkServiceInterface
{
    public function __construct(
        private readonly RpcClientInterface $rpcClient,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config
    ) {}

    /**
     * Get network details including status, chain ID, and current block number.
     *
     * @return NetworkDTO
     */
    public function getNetworkInfo(): NetworkDTO
    {
        $ttl = (int) $this->config->get('blockchain.cache_ttl.network', 15);

        $data = $this->cache->remember('blockchain:network', $ttl, function (): array {
            $startTime = microtime(true);

            try {
                $chainIdHex = (string) $this->rpcClient->call('eth_chainId');
                $blockNumberHex = (string) $this->rpcClient->call('eth_blockNumber');

                $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

                $chainId = (int) hexdec(str_replace('0x', '', $chainIdHex));
                $blockNumber = (int) hexdec(str_replace('0x', '', $blockNumberHex));

                return [
                    'chainId' => $chainId,
                    'networkName' => (string) $this->config->get('blockchain.network_name', 'sepolia'),
                    'blockNumber' => $blockNumber,
                    'rpcUrl' => (string) $this->config->get('blockchain.rpc_url'),
                    'isConnected' => true,
                    'latencyMs' => $latencyMs,
                ];
            } catch (Throwable) {
                return [
                    'chainId' => (int) $this->config->get('blockchain.chain_id', 11155111),
                    'networkName' => (string) $this->config->get('blockchain.network_name', 'sepolia'),
                    'blockNumber' => 0,
                    'rpcUrl' => (string) $this->config->get('blockchain.rpc_url'),
                    'isConnected' => false,
                    'latencyMs' => 0.0,
                ];
            }
        });

        $dto = new NetworkDTO(
            chainId: (int) ($data['chainId'] ?? 11155111),
            networkName: (string) ($data['networkName'] ?? 'sepolia'),
            blockNumber: (int) ($data['blockNumber'] ?? 0),
            rpcUrl: (string) ($data['rpcUrl'] ?? ''),
            isConnected: (bool) ($data['isConnected'] ?? false),
            latencyMs: (float) ($data['latencyMs'] ?? 0.0)
        );

        if (!$dto->isConnected) {
            $this->cache->forget('blockchain:network');
        }

        return $dto;
    }

    /**
     * Check if network node is healthy and reachable.
     *
     * @return bool
     */
    public function isHealthy(): bool
    {
        try {
            $info = $this->getNetworkInfo();
            $expectedChainId = (int) $this->config->get('blockchain.chain_id', 11155111);

            return $info->isConnected && ($info->chainId === $expectedChainId || $info->chainId > 0);
        } catch (Throwable) {
            return false;
        }
    }
}
