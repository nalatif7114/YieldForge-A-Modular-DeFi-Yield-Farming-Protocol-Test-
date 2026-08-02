<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class ProtocolAnalyticsDTO
{
    public function __construct(
        public float $tvlDailyChangePercentage,
        public float $tvlWeeklyChangePercentage,
        public float $tvlMonthlyChangePercentage,
        public int $activeWalletsCount,
        public int $newWalletsCount,
        public int $returningWalletsCount,
        public int $activePoolsCount,
        public int $totalTransactionsCount,
        public float $capitalEfficiencyRatio,
        public string $historicalHighTvlFormatted,
        public string $historicalLowTvlFormatted,
        public string $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'tvl_daily_change_percentage' => $this->tvlDailyChangePercentage,
            'tvl_weekly_change_percentage' => $this->tvlWeeklyChangePercentage,
            'tvl_monthly_change_percentage' => $this->tvlMonthlyChangePercentage,
            'active_wallets_count' => $this->activeWalletsCount,
            'new_wallets_count' => $this->newWalletsCount,
            'returning_wallets_count' => $this->returningWalletsCount,
            'active_pools_count' => $this->activePoolsCount,
            'total_transactions_count' => $this->totalTransactionsCount,
            'capital_efficiency_ratio' => $this->capitalEfficiencyRatio,
            'historical_high_tvl_formatted' => $this->historicalHighTvlFormatted,
            'historical_low_tvl_formatted' => $this->historicalLowTvlFormatted,
            'timestamp' => $this->timestamp,
        ];
    }
}
