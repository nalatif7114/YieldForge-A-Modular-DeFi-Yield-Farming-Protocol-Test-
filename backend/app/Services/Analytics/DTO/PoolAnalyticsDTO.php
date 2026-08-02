<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class PoolAnalyticsDTO
{
    public function __construct(
        public string $poolId,
        public string $tvlRaw,
        public string $tvlFormatted,
        public int $activeStakers,
        public string $averageStakeFormatted,
        public int $averageLockDuration,
        public float $averageApy,
        public string $depositVolumeFormatted,
        public string $withdrawalVolumeFormatted,
        public float $utilizationRate,
        public float $poolGrowthPercentage,
        public string $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'pool_id' => $this->poolId,
            'tvl_raw' => $this->tvlRaw,
            'tvl_formatted' => $this->tvlFormatted,
            'active_stakers' => $this->activeStakers,
            'average_stake_formatted' => $this->averageStakeFormatted,
            'average_lock_duration' => $this->averageLockDuration,
            'average_apy' => $this->averageApy,
            'deposit_volume_formatted' => $this->depositVolumeFormatted,
            'withdrawal_volume_formatted' => $this->withdrawalVolumeFormatted,
            'utilization_rate' => $this->utilizationRate,
            'pool_growth_percentage' => $this->poolGrowthPercentage,
            'timestamp' => $this->timestamp,
        ];
    }
}
