<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\BlockchainEvent;

use App\Models\PoolSnapshot;
use App\Models\ProtocolAnalytics;
use App\Services\Analytics\Contracts\AnalyticsCacheInterface;

use App\Services\Analytics\Contracts\AnalyticsServiceInterface;
use App\Services\Analytics\Contracts\BenchmarkCalculatorInterface;
use App\Services\Analytics\Contracts\KpiServiceInterface;

use App\Services\Analytics\DTO\APYHistoryDTO;
use App\Services\Analytics\DTO\PoolAnalyticsDTO;
use App\Services\Analytics\DTO\ProtocolAnalyticsDTO;
use App\Services\Analytics\DTO\RewardAnalyticsDTO;
use App\Services\Analytics\DTO\TVLHistoryDTO;
use App\Services\Analytics\DTO\WalletAnalyticsDTO;

class AnalyticsEngine implements AnalyticsServiceInterface
{
    public function __construct(
        private readonly KpiServiceInterface $kpiService,
        private readonly BenchmarkCalculatorInterface $benchmarkCalculator,
        private readonly TVLCalculator $tvlCalculator,
        private readonly APYCalculator $apyCalculator,
        private readonly PortfolioAnalyzer $portfolioAnalyzer,
        private readonly RewardAnalyzer $rewardAnalyzer,
        private readonly ChartDataBuilder $chartDataBuilder,
        private readonly HealthScoreCalculator $healthScoreCalculator,
        private readonly AnalyticsCacheInterface $analyticsCache
    ) {}

    public function getOverview(): array
    {
        return $this->analyticsCache->remember('overview', function (): array {
            $kpis = $this->kpiService->getProtocolKpis();
            $benchmarks = $this->benchmarkCalculator->getBenchmarks();

            return [
                'kpis' => $kpis->toArray(),
                'benchmarks' => $benchmarks->toArray(),
                'health_score' => $this->healthScoreCalculator->getHealthScore(),
                'timestamp' => now()->toIso8601String(),
            ];
        });
    }

    public function getTvlHistory(string $window = '30d'): TVLHistoryDTO
    {
        return $this->analyticsCache->remember('tvl_history_' . $window, function () use ($window): TVLHistoryDTO {
            $tvlRaw = $this->tvlCalculator->getCurrentTvlRaw();
            $tvlFormatted = $this->tvlCalculator->getCurrentTvlFormatted();
            $daily = $this->tvlCalculator->calculateGrowthPercentage(1);
            $weekly = $this->tvlCalculator->calculateGrowthPercentage(7);
            $monthly = $this->tvlCalculator->calculateGrowthPercentage(30);

            $points = $this->chartDataBuilder->getChartData('tvl', $window);

            return new TVLHistoryDTO(
                tvlRaw: $tvlRaw,
                tvlFormatted: $tvlFormatted,
                dailyChangePercentage: $daily,
                weeklyChangePercentage: $weekly,
                monthlyChangePercentage: $monthly,
                chartPoints: $points
            );
        });
    }

    public function getApyHistory(string $window = '30d'): APYHistoryDTO
    {
        return $this->analyticsCache->remember('apy_history_' . $window, function () use ($window): APYHistoryDTO {
            $avg = $this->apyCalculator->getAverageApy();
            $max = $this->apyCalculator->getHighestApy();
            $min = $this->apyCalculator->getLowestApy();

            $points = $this->chartDataBuilder->getChartData('apy', $window);

            return new APYHistoryDTO(
                averageApy: $avg,
                highestApy: $max,
                lowestApy: $min,
                chartPoints: $points
            );
        });
    }

    public function getProtocolAnalytics(): ProtocolAnalyticsDTO
    {
        return $this->analyticsCache->remember('protocol', function (): ProtocolAnalyticsDTO {
            /** @var ProtocolAnalytics|null $latest */
            $latest = ProtocolAnalytics::orderByDesc('timestamp')->first();

            return new ProtocolAnalyticsDTO(
                tvlDailyChangePercentage: $this->tvlCalculator->calculateGrowthPercentage(1),
                tvlWeeklyChangePercentage: $this->tvlCalculator->calculateGrowthPercentage(7),
                tvlMonthlyChangePercentage: $this->tvlCalculator->calculateGrowthPercentage(30),
                activeWalletsCount: $latest ? $latest->active_wallets_count : 0,
                newWalletsCount: $latest ? $latest->new_wallets_count : 0,
                returningWalletsCount: $latest ? $latest->returning_wallets_count : 0,
                activePoolsCount: $latest ? $latest->active_pools_count : 1,
                totalTransactionsCount: BlockchainEvent::count(),
                capitalEfficiencyRatio: 1.0,
                historicalHighTvlFormatted: $this->tvlCalculator->getCurrentTvlFormatted(),
                historicalLowTvlFormatted: $this->tvlCalculator->getCurrentTvlFormatted(),
                timestamp: now()->toIso8601String()
            );
        });
    }

    public function getPoolAnalytics(?string $poolId = null): PoolAnalyticsDTO|array
    {
        if ($poolId !== null) {
            /** @var PoolSnapshot|null $pool */
            $pool = PoolSnapshot::where('pool_id', $poolId)->first();

            return new PoolAnalyticsDTO(
                poolId: $poolId,
                tvlRaw: $pool ? $pool->total_staked_raw : '0',
                tvlFormatted: $pool ? $pool->total_staked_formatted : '0',
                activeStakers: 1,
                averageStakeFormatted: $pool ? $pool->total_staked_formatted : '0',
                averageLockDuration: 2592000,
                averageApy: $this->apyCalculator->getCurrentApy($poolId),
                depositVolumeFormatted: $pool ? $pool->total_staked_formatted : '0',
                withdrawalVolumeFormatted: '0',
                utilizationRate: 100.0,
                poolGrowthPercentage: 0.0,
                timestamp: now()->toIso8601String()
            );
        }

        $allPools = PoolSnapshot::all();

        return $allPools->map(function (PoolSnapshot $pool) {
            return new PoolAnalyticsDTO(
                poolId: $pool->pool_id,
                tvlRaw: $pool->total_staked_raw,
                tvlFormatted: $pool->total_staked_formatted,
                activeStakers: 1,
                averageStakeFormatted: $pool->total_staked_formatted,
                averageLockDuration: 2592000,
                averageApy: $this->apyCalculator->getCurrentApy($pool->pool_id),
                depositVolumeFormatted: $pool->total_staked_formatted,
                withdrawalVolumeFormatted: '0',
                utilizationRate: 100.0,
                poolGrowthPercentage: 0.0,
                timestamp: now()->toIso8601String()
            );
        })->toArray();
    }

    public function getWalletAnalytics(string $wallet): WalletAnalyticsDTO
    {
        return $this->portfolioAnalyzer->analyze($wallet);
    }

    public function getRewardAnalytics(): RewardAnalyticsDTO
    {
        return $this->analyticsCache->remember('rewards', function (): RewardAnalyticsDTO {
            return $this->rewardAnalyzer->analyze();
        });
    }
}
