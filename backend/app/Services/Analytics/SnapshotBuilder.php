<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\AnalyticsSnapshot;
use App\Models\BlockchainEvent;
use App\Models\PoolAnalytics;
use App\Models\PoolSnapshot;
use App\Models\ProtocolAnalytics;
use App\Models\WalletPosition;
use App\Services\Blockchain\Support\EthereumCodec;

class SnapshotBuilder
{
    public function __construct(
        private readonly TVLCalculator $tvlCalculator,
        private readonly APYCalculator $apyCalculator,
        private readonly EthereumCodec $codec
    ) {}

    public function buildSnapshot(): AnalyticsSnapshot
    {
        $tvlRaw = $this->tvlCalculator->getCurrentTvlRaw();
        $tvlFormatted = $this->tvlCalculator->getCurrentTvlFormatted();
        $apy = $this->apyCalculator->getCurrentApy();
        $activeStakers = WalletPosition::where('staked_balance_raw', '>', '0')->count();
        $version = (string) config('blockchain.analytics_version', '1.0.0');

        $snapshot = AnalyticsSnapshot::create([
            'chain_id' => (int) config('blockchain.chain_id', 11155111),
            'network' => (string) config('blockchain.network_name', 'sepolia'),
            'snapshot_type' => '5m',
            'analytics_version' => $version,
            'total_tvl_raw' => $tvlRaw,
            'total_tvl_formatted' => $tvlFormatted,
            'average_apy' => $apy,
            'active_stakers' => $activeStakers,
            'total_rewards_raw' => '0',
            'metric_name' => 'tvl',
            'metric_value' => (float) $tvlFormatted,
            'aggregation_window' => '5m',
            'source' => 'indexer_engine',
            'metadata' => [
                'active_stakers' => $activeStakers,
                'average_apy' => $apy,
            ],
            'timestamp' => now(),
        ]);

        // Also record pool analytics snapshot
        /** @var PoolSnapshot|null $pool */
        $pool = PoolSnapshot::where('pool_id', 'pool-1')->first();
        if ($pool) {
            PoolAnalytics::create([
                'pool_id' => $pool->pool_id,
                'analytics_version' => $version,
                'tvl_raw' => $pool->total_staked_raw,
                'tvl_formatted' => $pool->total_staked_formatted,
                'active_stakers' => $activeStakers,
                'average_stake_formatted' => $activeStakers > 0 ? (string) round((float) $pool->total_staked_formatted / $activeStakers, 4) : '0',
                'average_lock_duration' => 2592000, // 30 days default
                'average_apy' => $apy,
                'deposit_volume_formatted' => $pool->total_staked_formatted,
                'withdrawal_volume_formatted' => '0',
                'utilization_rate' => 100.0,
                'pool_growth_percentage' => 0.0,
                'timestamp' => now(),
            ]);
        }

        // Record protocol analytics record
        ProtocolAnalytics::create([
            'analytics_version' => $version,
            'tvl_daily_change_percentage' => $this->tvlCalculator->calculateGrowthPercentage(1),
            'tvl_weekly_change_percentage' => $this->tvlCalculator->calculateGrowthPercentage(7),
            'tvl_monthly_change_percentage' => $this->tvlCalculator->calculateGrowthPercentage(30),
            'active_wallets_count' => WalletPosition::count(),
            'new_wallets_count' => WalletPosition::where('created_at', '>=', now()->subDay())->count(),
            'returning_wallets_count' => WalletPosition::where('updated_at', '>=', now()->subDay())->count(),
            'active_pools_count' => 1,
            'total_transactions_count' => BlockchainEvent::count(),
            'capital_efficiency_ratio' => 1.0,
            'historical_high_tvl_formatted' => $tvlFormatted,
            'historical_low_tvl_formatted' => $tvlFormatted,
            'timestamp' => now(),
        ]);

        return $snapshot;
    }
}
