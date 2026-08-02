<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\DailyStatistic;
use App\Models\PoolSnapshot;
use App\Services\Blockchain\Support\EthereumCodec;

class TVLCalculator
{
    public function __construct(
        private readonly EthereumCodec $codec
    ) {}

    public function getCurrentTvlRaw(): string
    {
        /** @var PoolSnapshot|null $pool */
        $pool = PoolSnapshot::where('pool_id', 'pool-1')->first();

        return $pool ? $pool->total_staked_raw : '0';
    }

    public function getCurrentTvlFormatted(): string
    {
        return $this->codec->formatUnits($this->getCurrentTvlRaw(), 18);
    }

    public function calculateGrowthPercentage(int $days = 1): float
    {
        $current = (float) $this->getCurrentTvlFormatted();
        if ($current <= 0.0) {
            return 0.0;
        }

        /** @var DailyStatistic|null $past */
        $past = DailyStatistic::query()
            ->where('timestamp', '<=', now()->subDays($days))
            ->orderByDesc('timestamp')
            ->first();

        if (!$past) {
            return 0.0;
        }

        $pastVal = (float) $past->tvl_formatted;
        if ($pastVal <= 0.0) {
            return 100.0;
        }

        return round((($current - $pastVal) / $pastVal) * 100.0, 2);
    }
}
