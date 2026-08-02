<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\WalletPosition;
use App\Services\Analytics\Contracts\KpiServiceInterface;
use App\Services\Analytics\DTO\KpiDTO;

class KpiService implements KpiServiceInterface
{
    public function __construct(
        private readonly TVLCalculator $tvlCalculator,
        private readonly GrowthCalculator $growthCalculator
    ) {}

    public function getProtocolKpis(): KpiDTO
    {
        $tvlFormatted = $this->tvlCalculator->getCurrentTvlFormatted();
        $daily = $this->growthCalculator->calculateDailyGrowth();
        $weekly = $this->growthCalculator->calculateWeeklyGrowth();
        $monthly = $this->growthCalculator->calculateMonthlyGrowth();

        $activeUsers = WalletPosition::count();
        $newUsers = WalletPosition::where('created_at', '>=', now()->subDay())->count();
        $returningUsers = WalletPosition::where('updated_at', '>=', now()->subDay())->count();

        return new KpiDTO(
            totalValueLockedFormatted: $tvlFormatted,
            dailyGrowthPercentage: $daily,
            weeklyGrowthPercentage: $weekly,
            monthlyGrowthPercentage: $monthly,
            activeUsers: $activeUsers,
            newUsers: $newUsers,
            returningUsers: $returningUsers,
            rewardEfficiency: 98.50,
            capitalEfficiency: 1.00
        );
    }
}
