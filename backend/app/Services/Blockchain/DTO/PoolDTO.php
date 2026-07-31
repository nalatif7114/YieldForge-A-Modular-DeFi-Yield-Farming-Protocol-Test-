<?php

declare(strict_types=1);

namespace App\Services\Blockchain\DTO;

readonly class PoolDTO
{
    public function __construct(
        public string $poolId,
        public string $contractAddress,
        public string $stakingTokenAddress,
        public string $stakingTokenName,
        public string $stakingTokenSymbol,
        public int $stakingTokenDecimals,
        public string $totalStakedRaw,
        public string $totalStakedFormatted,
        public bool $isPaused
    ) {}

    /**
     * Convert DTO to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'pool_id' => $this->poolId,
            'contract_address' => $this->contractAddress,
            'staking_token' => [
                'address' => $this->stakingTokenAddress,
                'name' => $this->stakingTokenName,
                'symbol' => $this->stakingTokenSymbol,
                'decimals' => $this->stakingTokenDecimals,
            ],
            'total_staked_raw' => $this->totalStakedRaw,
            'total_staked_formatted' => $this->totalStakedFormatted,
            'is_paused' => $this->isPaused,
        ];
    }
}
