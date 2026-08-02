<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class RewardAnalyticsDTO
{
    public function __construct(
        public string $totalRewardsDistributedRaw,
        public string $totalRewardsDistributedFormatted,
        public float $rewardVelocity,
        public array $topEarners,
        public string $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'total_rewards_distributed_raw' => $this->totalRewardsDistributedRaw,
            'total_rewards_distributed_formatted' => $this->totalRewardsDistributedFormatted,
            'reward_velocity' => $this->rewardVelocity,
            'top_earners' => $this->topEarners,
            'timestamp' => $this->timestamp,
        ];
    }
}
