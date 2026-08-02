<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Services\Blockchain\Support\EthereumCodec;

class YieldCalculator
{
    public function __construct(
        private readonly EthereumCodec $codec
    ) {}

    public function calculateRoi(string $stakedRaw, string $rewardsRaw): float
    {
        if ($stakedRaw === '0' || $stakedRaw === '') {
            return 0.0;
        }

        $staked = (float) $this->codec->formatUnits($stakedRaw, 18);
        $rewards = (float) $this->codec->formatUnits($rewardsRaw, 18);

        if ($staked <= 0.0) {
            return 0.0;
        }

        return round(($rewards / $staked) * 100.0, 2);
    }

    public function calculateCompoundedYield(string $stakedRaw, float $apy, int $days = 365): string
    {
        $principal = (float) $this->codec->formatUnits($stakedRaw, 18);
        if ($principal <= 0.0) {
            return '0';
        }

        $r = $apy / 100.0;
        $n = 365; // Daily compounding
        $t = $days / 365.0;

        $amount = $principal * pow((1 + ($r / $n)), $n * $t);
        $yield = max(0.0, $amount - $principal);

        return number_format($yield, 4, '.', '');
    }
}
