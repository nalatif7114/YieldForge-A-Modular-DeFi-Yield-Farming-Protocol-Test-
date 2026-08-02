<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\RewardSnapshot;
use App\Models\WalletPosition;
use App\Services\Analytics\DTO\RewardAnalyticsDTO;
use App\Services\Blockchain\Support\EthereumCodec;

class RewardAnalyzer
{
    public function __construct(
        private readonly EthereumCodec $codec
    ) {}

    public function analyze(): RewardAnalyticsDTO
    {
        $topEarnersModels = WalletPosition::query()
            ->where('token_balance_raw', '>', '0')
            ->orderByDesc('token_balance_raw')
            ->limit(10)
            ->get();

        $topEarners = $topEarnersModels->map(function (WalletPosition $w) {
            return [
                'wallet' => $w->wallet,
                'balance_raw' => $w->token_balance_raw,
                'balance_formatted' => $w->token_balance_formatted,
            ];
        })->toArray();

        $totalRaw = '0';
        foreach ($topEarnersModels as $w) {
            $totalRaw = (string) bcadd($totalRaw, $w->token_balance_raw);
        }
        $totalFormatted = $this->codec->formatUnits($totalRaw, 18);

        // Velocity: rewards distributed per day
        $velocity = (float) $totalFormatted > 0 ? round((float) $totalFormatted / 30.0, 4) : 0.0;

        return new RewardAnalyticsDTO(
            totalRewardsDistributedRaw: $totalRaw,
            totalRewardsDistributedFormatted: $totalFormatted,
            rewardVelocity: $velocity,
            topEarners: $topEarners,
            timestamp: now()->toIso8601String()
        );
    }
}
