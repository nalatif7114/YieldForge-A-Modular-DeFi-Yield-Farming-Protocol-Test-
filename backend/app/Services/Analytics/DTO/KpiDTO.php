<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class KpiDTO
{
    public function __construct(
        public string $totalValueLockedFormatted,
        public float $dailyGrowthPercentage,
        public float $weeklyGrowthPercentage,
        public float $monthlyGrowthPercentage,
        public int $activeUsers,
        public int $newUsers,
        public int $returningUsers,
        public float $rewardEfficiency,
        public float $capitalEfficiency
    ) {}

    public function toArray(): array
    {
        return [
            'total_value_locked_formatted' => $this->totalValueLockedFormatted,
            'daily_growth_percentage' => $this->dailyGrowthPercentage,
            'weekly_growth_percentage' => $this->weeklyGrowthPercentage,
            'monthly_growth_percentage' => $this->monthlyGrowthPercentage,
            'active_users' => $this->activeUsers,
            'new_users' => $this->newUsers,
            'returning_users' => $this->returningUsers,
            'reward_efficiency' => $this->rewardEfficiency,
            'capital_efficiency' => $this->capitalEfficiency,
        ];
    }
}
