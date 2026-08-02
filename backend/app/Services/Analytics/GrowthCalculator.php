<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\DailyStatistic;

class GrowthCalculator
{
    public function calculateDailyGrowth(): float
    {
        return $this->getGrowthForDays(1);
    }

    public function calculateWeeklyGrowth(): float
    {
        return $this->getGrowthForDays(7);
    }

    public function calculateMonthlyGrowth(): float
    {
        return $this->getGrowthForDays(30);
    }

    private function getGrowthForDays(int $days): float
    {
        /** @var DailyStatistic|null $latest */
        $latest = DailyStatistic::orderByDesc('timestamp')->first();
        /** @var DailyStatistic|null $previous */
        $previous = DailyStatistic::where('timestamp', '<=', now()->subDays($days))->orderByDesc('timestamp')->first();

        if (!$latest || !$previous) {
            return 0.0;
        }

        $v1 = (float) $latest->tvl_formatted;
        $v0 = (float) $previous->tvl_formatted;

        if ($v0 <= 0.0) {
            return $v1 > 0 ? 100.0 : 0.0;
        }

        return round((($v1 - $v0) / $v0) * 100.0, 2);
    }
}
