<?php

declare(strict_types=1);

namespace App\Services\Research;

use App\Models\DailyStatistic;
use App\Models\HourlyStatistic;
use Illuminate\Support\Collection;

class ResearchTimeSeriesBuilder
{
    /**
     * Build research time series for specified aggregation interval.
     *
     * @param string $interval (hourly, daily, weekly, monthly)
     * @param int $periods
     * @return Collection<int, mixed>
     */
    public function buildSeries(string $interval = 'daily', int $periods = 30): Collection
    {
        if ($interval === 'hourly') {
            return HourlyStatistic::query()
                ->orderByDesc('timestamp')
                ->limit($periods)
                ->get();
        }

        return DailyStatistic::query()
            ->orderByDesc('timestamp')
            ->limit($periods)
            ->get();
    }
}
