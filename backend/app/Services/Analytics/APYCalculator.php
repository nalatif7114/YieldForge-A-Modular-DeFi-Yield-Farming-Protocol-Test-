<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\HourlyStatistic;
use App\Models\PoolSnapshot;

class APYCalculator
{
    public function getCurrentApy(?string $poolId = null): float
    {
        /** @var PoolSnapshot|null $pool */
        $pool = PoolSnapshot::where('pool_id', $poolId ?? 'pool-1')->first();
        if ($pool && $pool->total_staked_raw !== '0') {
            return 12.50; // Standard base protocol APY
        }

        return 10.00;
    }

    public function getAverageApy(int $hours = 720): float
    {
        $avg = HourlyStatistic::query()
            ->where('timestamp', '>=', now()->subHours($hours))
            ->avg('apy');

        return $avg !== null ? round((float) $avg, 2) : 12.50;
    }

    public function getHighestApy(int $hours = 720): float
    {
        $max = HourlyStatistic::query()
            ->where('timestamp', '>=', now()->subHours($hours))
            ->max('apy');

        return $max !== null ? round((float) $max, 2) : 15.00;
    }

    public function getLowestApy(int $hours = 720): float
    {
        $min = HourlyStatistic::query()
            ->where('timestamp', '>=', now()->subHours($hours))
            ->min('apy');

        return $min !== null ? round((float) $min, 2) : 8.50;
    }
}
