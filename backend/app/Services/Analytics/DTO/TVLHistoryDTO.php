<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class TVLHistoryDTO
{
    /**
     * @param array<int, ChartPointDTO> $chartPoints
     */
    public function __construct(
        public string $tvlRaw,
        public string $tvlFormatted,
        public float $dailyChangePercentage,
        public float $weeklyChangePercentage,
        public float $monthlyChangePercentage,
        public array $chartPoints = []
    ) {}

    public function toArray(): array
    {
        return [
            'tvl_raw' => $this->tvlRaw,
            'tvl_formatted' => $this->tvlFormatted,
            'daily_change_percentage' => $this->dailyChangePercentage,
            'weekly_change_percentage' => $this->weeklyChangePercentage,
            'monthly_change_percentage' => $this->monthlyChangePercentage,
            'points' => array_map(fn (ChartPointDTO $point) => $point->toArray(), $this->chartPoints),
        ];
    }
}
