<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\BlockchainEvent;
use App\Models\DailyStatistic;
use App\Models\HourlyStatistic;
use App\Models\WalletPosition;

class TimeSeriesBuilder
{
    public function __construct(
        private readonly TVLCalculator $tvlCalculator,
        private readonly APYCalculator $apyCalculator
    ) {}

    public function generateHourly(): HourlyStatistic
    {
        $tvlFormatted = $this->tvlCalculator->getCurrentTvlFormatted();
        $apy = $this->apyCalculator->getCurrentApy();
        $version = (string) config('blockchain.analytics_version', '1.0.0');

        $txCount = BlockchainEvent::where('created_at', '>=', now()->subHour())->count();
        $activeUsers = WalletPosition::where('updated_at', '>=', now()->subHour())->count();

        return HourlyStatistic::create([
            'analytics_version' => $version,
            'timestamp' => now()->startOfHour(),
            'tvl_formatted' => $tvlFormatted,
            'apy' => $apy,
            'volume_formatted' => $tvlFormatted,
            'tx_count' => $txCount,
            'active_users' => $activeUsers,
        ]);
    }

    public function generateDaily(): DailyStatistic
    {
        $tvlFormatted = $this->tvlCalculator->getCurrentTvlFormatted();
        $apy = $this->apyCalculator->getCurrentApy();
        $version = (string) config('blockchain.analytics_version', '1.0.0');

        $txCount = BlockchainEvent::where('created_at', '>=', now()->subDay())->count();
        $activeUsers = WalletPosition::where('updated_at', '>=', now()->subDay())->count();

        return DailyStatistic::create([
            'analytics_version' => $version,
            'timestamp' => now()->startOfDay(),
            'tvl_formatted' => $tvlFormatted,
            'apy' => $apy,
            'volume_formatted' => $tvlFormatted,
            'tx_count' => $txCount,
            'active_users' => $activeUsers,
        ]);
    }
}
