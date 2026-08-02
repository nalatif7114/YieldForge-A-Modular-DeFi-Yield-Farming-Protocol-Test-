<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\DailyStatistic;
use App\Services\Analytics\Contracts\BenchmarkCalculatorInterface;
use App\Services\Analytics\DTO\BenchmarkDTO;

class BenchmarkCalculator implements BenchmarkCalculatorInterface
{
    public function __construct(
        private readonly TVLCalculator $tvlCalculator,
        private readonly APYCalculator $apyCalculator
    ) {}

    public function getBenchmarks(): BenchmarkDTO
    {
        $tvl24h = $this->tvlCalculator->calculateGrowthPercentage(1);
        $tvl7d = $this->tvlCalculator->calculateGrowthPercentage(7);
        $apy30d = $this->apyCalculator->getAverageApy(720);

        /** @var DailyStatistic|null $high */
        $high = DailyStatistic::orderByDesc('tvl_formatted')->first();
        /** @var DailyStatistic|null $low */
        $low = DailyStatistic::where('tvl_formatted', '>', '0')->orderBy('tvl_formatted', 'asc')->first();

        $highFormatted = $high ? $high->tvl_formatted : $this->tvlCalculator->getCurrentTvlFormatted();
        $lowFormatted = $low ? $low->tvl_formatted : $this->tvlCalculator->getCurrentTvlFormatted();

        return new BenchmarkDTO(
            tvl24hChangePercentage: $tvl24h,
            tvl7dChangePercentage: $tvl7d,
            apy30dAverage: $apy30d,
            historicalHighTvlFormatted: $highFormatted,
            historicalLowTvlFormatted: $lowFormatted
        );
    }
}
