<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\AnalyticsSnapshot;
use App\Models\DailyStatistic;
use App\Models\HourlyStatistic;
use App\Services\Analytics\Contracts\HistoricalDataInterface;
use App\Services\Analytics\DTO\ChartPointDTO;

class ChartDataBuilder implements HistoricalDataInterface
{
    public function __construct(
        private readonly TVLCalculator $tvlCalculator,
        private readonly APYCalculator $apyCalculator
    ) {}

    public function getChartData(string $metric, string $window = '30d'): array
    {
        $points = [];

        if (in_array($window, ['24h', '5m', '15m', '1h', '6h'])) {
            $records = HourlyStatistic::query()
                ->where('timestamp', '>=', now()->subHours(24))
                ->orderBy('timestamp', 'asc')
                ->get();

            foreach ($records as $rec) {
                $val = match ($metric) {
                    'tvl' => (float) $rec->tvl_formatted,
                    'apy' => (float) $rec->apy,
                    'rewards' => (float) $rec->volume_formatted,
                    'transactions' => (float) $rec->tx_count,
                    default => (float) $rec->tvl_formatted,
                };

                $points[] = new ChartPointDTO(
                    timestamp: $rec->timestamp->toIso8601String(),
                    value: $val
                );
            }
        } else {
            $days = match ($window) {
                '7d' => 7,
                '365d' => 365,
                default => 30,
            };

            $records = DailyStatistic::query()
                ->where('timestamp', '>=', now()->subDays($days))
                ->orderBy('timestamp', 'asc')
                ->get();

            foreach ($records as $rec) {
                $val = match ($metric) {
                    'tvl' => (float) $rec->tvl_formatted,
                    'apy' => (float) $rec->apy,
                    'rewards' => (float) $rec->volume_formatted,
                    'transactions' => (float) $rec->tx_count,
                    default => (float) $rec->tvl_formatted,
                };

                $points[] = new ChartPointDTO(
                    timestamp: $rec->timestamp->toIso8601String(),
                    value: $val
                );
            }
        }

        // Fallback point if database has no aggregated history yet
        if (empty($points)) {
            $currentVal = match ($metric) {
                'tvl' => (float) $this->tvlCalculator->getCurrentTvlFormatted(),
                'apy' => $this->apyCalculator->getCurrentApy(),
                'rewards' => 0.0,
                'transactions' => 1.0,
                default => 0.0,
            };

            $points[] = new ChartPointDTO(
                timestamp: now()->toIso8601String(),
                value: $currentVal
            );
        }

        return $points;
    }

    public function cleanupOldData(): int
    {
        $retentionDays = (int) config('blockchain.history_retention_days', 365);
        $cutoff = now()->subDays($retentionDays);

        $snapshotsDeleted = AnalyticsSnapshot::where('timestamp', '<', $cutoff)->delete();
        $hourlyDeleted = HourlyStatistic::where('timestamp', '<', $cutoff)->delete();
        $dailyDeleted = DailyStatistic::where('timestamp', '<', $cutoff)->delete();

        return $snapshotsDeleted + $hourlyDeleted + $dailyDeleted;
    }
}
