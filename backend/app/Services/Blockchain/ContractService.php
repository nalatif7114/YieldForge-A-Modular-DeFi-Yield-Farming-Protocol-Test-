<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Services\Blockchain\Contracts\ContractServiceInterface;
use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\DTO\PoolDTO;
use App\Services\Blockchain\DTO\RewardDTO;
use App\Services\Blockchain\DTO\StakeDTO;
use App\Services\Blockchain\Exceptions\BlockchainException;
use App\Services\Blockchain\Support\EthereumCodec;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class ContractService implements ContractServiceInterface
{
    public function __construct(
        private readonly RpcClientInterface $rpcClient,
        private readonly EthereumCodec $codec,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config
    ) {}

    /**
     * Get list of configured smart contracts metadata.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getContracts(): array
    {
        $ttl = (int) $this->config->get('blockchain.cache_ttl.contracts', 300);

        return $this->cache->remember('blockchain:contracts', $ttl, function (): array {
            $contractsConfig = (array) $this->config->get('blockchain.contracts', []);
            $result = [];

            foreach ($contractsConfig as $key => $conf) {
                $result[] = [
                    'key' => $key,
                    'name' => $conf['name'] ?? $key,
                    'type' => $conf['type'] ?? 'unknown',
                    'address' => $conf['address'] ?? '',
                    'abi_file' => $conf['abi'] ?? '',
                ];
            }

            return $result;
        });
    }

    /**
     * Get yield farming / staking pools information.
     *
     * @return array<int, PoolDTO>
     * @throws BlockchainException
     */
    public function getPools(): array
    {
        $ttl = (int) $this->config->get('blockchain.cache_ttl.pools', 30);

        return $this->cache->remember('blockchain:pools', $ttl, function (): array {
            $stakingAddress = (string) $this->config->get('blockchain.contracts.staking.address');
            $tokenAddress = (string) $this->config->get('blockchain.contracts.token.address');

            try {
                // Call staking contract view functions
                $stakingTokenRaw = $this->callView($stakingAddress, 'stakingToken()');
                $decodedStakingAddr = $stakingTokenRaw ? $this->codec->decodeAddress($stakingTokenRaw) : null;
                $stakingTokenAddr = ($decodedStakingAddr && $decodedStakingAddr !== '0x0000000000000000000000000000000000000000')
                    ? $decodedStakingAddr
                    : $tokenAddress;

                $totalStakedHex = $this->callView($stakingAddress, 'totalStaked()');
                $totalStakedRaw = $totalStakedHex ? $this->codec->decodeUint256($totalStakedHex) : '0';

                $pausedHex = $this->callView($stakingAddress, 'paused()');
                $isPaused = $pausedHex ? $this->codec->decodeBool($pausedHex) : false;

                // Call token contract view functions
                $nameHex = $this->callView($tokenAddress, 'name()');
                $decodedName = $nameHex ? $this->codec->decodeString($nameHex) : '';
                $tokenName = $decodedName !== '' ? $decodedName : 'YieldForge Token';

                $symbolHex = $this->callView($tokenAddress, 'symbol()');
                $decodedSymbol = $symbolHex ? $this->codec->decodeString($symbolHex) : '';
                $tokenSymbol = $decodedSymbol !== '' ? $decodedSymbol : 'YFT';

                $decimalsHex = $this->callView($tokenAddress, 'decimals()');
                $decimals = $decimalsHex ? (int) $this->codec->decodeUint256($decimalsHex) : 18;
                if ($decimals === 0) {
                    $decimals = 18;
                }

                $formattedTotalStaked = $this->codec->formatUnits($totalStakedRaw, $decimals);

                $pool = new PoolDTO(
                    poolId: 'pool-1',
                    contractAddress: $stakingAddress,
                    stakingTokenAddress: $stakingTokenAddr,
                    stakingTokenName: $tokenName,
                    stakingTokenSymbol: $tokenSymbol,
                    stakingTokenDecimals: $decimals,
                    totalStakedRaw: $totalStakedRaw,
                    totalStakedFormatted: $formattedTotalStaked,
                    isPaused: $isPaused
                );

                return [$pool];
            } catch (Throwable $e) {
                throw new BlockchainException("Failed to fetch pool details from blockchain: {$e->getMessage()}", 500, $e);
            }
        });
    }

    /**
     * Get stake details for a specific wallet address.
     *
     * @param string $wallet
     * @return StakeDTO
     * @throws BlockchainException
     */
    public function getStakeInfo(string $wallet): StakeDTO
    {
        $walletLower = strtolower($wallet);
        $ttl = (int) $this->config->get('blockchain.cache_ttl.stakes', 15);

        return $this->cache->remember("blockchain:stakes:{$walletLower}", $ttl, function () use ($wallet): StakeDTO {
            $stakingAddress = (string) $this->config->get('blockchain.contracts.staking.address');

            try {
                $balanceHex = $this->callView($stakingAddress, 'balanceOf(address)', [$wallet]);
                $stakedRaw = $balanceHex ? $this->codec->decodeUint256($balanceHex) : '0';

                $totalStakedHex = $this->callView($stakingAddress, 'totalStaked()');
                $totalStakedRaw = $totalStakedHex ? $this->codec->decodeUint256($totalStakedHex) : '0';

                $decimals = 18;
                $formattedStaked = $this->codec->formatUnits($stakedRaw, $decimals);

                $sharePercentage = 0.0;
                if ((float) $totalStakedRaw > 0 && (float) $stakedRaw > 0) {
                    $sharePercentage = round(((float) $stakedRaw / (float) $totalStakedRaw) * 100, 4);
                }

                return new StakeDTO(
                    wallet: $wallet,
                    stakingContract: $stakingAddress,
                    stakedBalanceRaw: $stakedRaw,
                    stakedBalanceFormatted: $formattedStaked,
                    poolSharePercentage: $sharePercentage
                );
            } catch (Throwable $e) {
                throw new BlockchainException("Failed to fetch stake info for wallet [{$wallet}]: {$e->getMessage()}", 500, $e);
            }
        });
    }

    /**
     * Get token balances and reward details for a wallet address.
     *
     * @param string $wallet
     * @return RewardDTO
     * @throws BlockchainException
     */
    public function getRewardInfo(string $wallet): RewardDTO
    {
        $walletLower = strtolower($wallet);
        $ttl = (int) $this->config->get('blockchain.cache_ttl.rewards', 15);

        return $this->cache->remember("blockchain:rewards:{$walletLower}", $ttl, function () use ($wallet): RewardDTO {
            $tokenAddress = (string) $this->config->get('blockchain.contracts.token.address');

            try {
                $balanceHex = $this->callView($tokenAddress, 'balanceOf(address)', [$wallet]);
                $balanceRaw = $balanceHex ? $this->codec->decodeUint256($balanceHex) : '0';

                $symbolHex = $this->callView($tokenAddress, 'symbol()');
                $decodedSymbol = $symbolHex ? $this->codec->decodeString($symbolHex) : '';
                $symbol = $decodedSymbol !== '' ? $decodedSymbol : 'YFT';

                $decimalsHex = $this->callView($tokenAddress, 'decimals()');
                $decimals = $decimalsHex ? (int) $this->codec->decodeUint256($decimalsHex) : 18;
                if ($decimals === 0) {
                    $decimals = 18;
                }

                $formattedBalance = $this->codec->formatUnits($balanceRaw, $decimals);

                return new RewardDTO(
                    wallet: $wallet,
                    tokenAddress: $tokenAddress,
                    tokenSymbol: $symbol,
                    balanceRaw: $balanceRaw,
                    balanceFormatted: $formattedBalance,
                    pendingRewardsRaw: '0',
                    pendingRewardsFormatted: '0'
                );
            } catch (Throwable $e) {
                throw new BlockchainException("Failed to fetch reward info for wallet [{$wallet}]: {$e->getMessage()}", 500, $e);
            }
        });
    }

    /**
     * Execute contract call using RpcClient.
     *
     * @param string $to
     * @param string $signature
     * @param array<int, mixed> $args
     * @return string|null
     */
    private function callView(string $to, string $signature, array $args = []): ?string
    {
        $data = $this->codec->encodeCall($signature, $args);

        $result = $this->rpcClient->call('eth_call', [
            [
                'to' => $to,
                'data' => $data,
            ],
            'latest',
        ]);

        return is_string($result) ? $result : null;
    }
}
