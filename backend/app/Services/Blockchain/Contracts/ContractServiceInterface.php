<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Contracts;

use App\Services\Blockchain\DTO\PoolDTO;
use App\Services\Blockchain\DTO\RewardDTO;
use App\Services\Blockchain\DTO\StakeDTO;

interface ContractServiceInterface
{
    /**
     * Get list of configured smart contracts metadata.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getContracts(): array;

    /**
     * Get yield farming / staking pools information.
     *
     * @return array<int, PoolDTO>
     */
    public function getPools(): array;

    /**
     * Get stake details for a specific wallet address.
     *
     * @param string $wallet
     * @return StakeDTO
     */
    public function getStakeInfo(string $wallet): StakeDTO;

    /**
     * Get token balances and reward details for a wallet address.
     *
     * @param string $wallet
     * @return RewardDTO
     */
    public function getRewardInfo(string $wallet): RewardDTO;
}
