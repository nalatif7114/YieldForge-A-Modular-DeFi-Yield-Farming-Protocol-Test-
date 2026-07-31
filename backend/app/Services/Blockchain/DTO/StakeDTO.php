<?php

declare(strict_types=1);

namespace App\Services\Blockchain\DTO;

readonly class StakeDTO
{
    public function __construct(
        public string $wallet,
        public string $stakingContract,
        public string $stakedBalanceRaw,
        public string $stakedBalanceFormatted,
        public float $poolSharePercentage
    ) {}

    /**
     * Convert DTO to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'wallet' => $this->wallet,
            'staking_contract' => $this->stakingContract,
            'staked_balance_raw' => $this->stakedBalanceRaw,
            'staked_balance_formatted' => $this->stakedBalanceFormatted,
            'pool_share_percentage' => $this->poolSharePercentage,
        ];
    }
}
