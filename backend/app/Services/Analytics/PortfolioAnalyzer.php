<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\RewardSnapshot;
use App\Models\TransactionHistory;
use App\Models\WalletPosition;
use App\Services\Analytics\DTO\WalletAnalyticsDTO;
use App\Services\Blockchain\Support\EthereumCodec;

class PortfolioAnalyzer
{
    public function __construct(
        private readonly EthereumCodec $codec,
        private readonly YieldCalculator $yieldCalculator,
        private readonly APYCalculator $apyCalculator
    ) {}

    public function analyze(string $wallet): WalletAnalyticsDTO
    {
        $walletLower = strtolower($wallet);
        /** @var WalletPosition|null $position */
        $position = WalletPosition::where('wallet', $walletLower)->first();

        /** @var RewardSnapshot|null $reward */
        $reward = RewardSnapshot::where('wallet', $walletLower)->first();

        $stakedRaw = $position ? $position->staked_balance_raw : '0';
        $stakedFormatted = $position ? $position->staked_balance_formatted : '0';

        $tokenRaw = $position ? $position->token_balance_raw : ($reward ? $reward->balance_raw : '0');
        $tokenFormatted = $position ? $position->token_balance_formatted : ($reward ? $reward->balance_formatted : '0');

        $pendingRaw = $reward ? $reward->pending_rewards_raw : '0';
        $pendingFormatted = $reward ? $reward->pending_rewards_formatted : '0';

        $roi = $this->yieldCalculator->calculateRoi($stakedRaw, $tokenRaw);
        $apy = $this->apyCalculator->getCurrentApy();
        $compoundedYield = $this->yieldCalculator->calculateCompoundedYield($stakedRaw, $apy, 365);

        /** @var TransactionHistory|null $lastTx */
        $lastTx = TransactionHistory::where('wallet', $walletLower)->orderByDesc('timestamp')->first();
        $lastActive = $lastTx ? $lastTx->timestamp?->toIso8601String() : null;

        $poolAllocation = [
            [
                'pool_id' => 'pool-1',
                'pool_name' => 'YieldForge Core Staking',
                'staked_raw' => $stakedRaw,
                'staked_formatted' => $stakedFormatted,
                'share_percentage' => $position ? $position->pool_share_percentage : 0.0,
            ],
        ];

        // Risk metrics calculation
        $diversificationScore = 100.00; // Single pool active
        $concentrationRisk = 100.00;
        $largestPoolExposure = 'pool-1';
        $impermanentRiskEstimate = 0.00; // Single staking asset (no IL)
        $stakedNum = (float) $stakedFormatted;
        $tokenNum = (float) $tokenFormatted;
        $rewardDepRatio = ($stakedNum + $tokenNum) > 0 ? round(($tokenNum / ($stakedNum + $tokenNum)) * 100.0, 2) : 0.0;

        return new WalletAnalyticsDTO(
            wallet: $wallet,
            tvlRaw: $stakedRaw,
            tvlFormatted: $stakedFormatted,
            rewardsRaw: $tokenRaw,
            rewardsFormatted: $tokenFormatted,
            pendingRewardsRaw: $pendingRaw,
            pendingRewardsFormatted: $pendingFormatted,
            roiPercentage: $roi,
            apyPercentage: $apy,
            compoundedYieldFormatted: $compoundedYield,
            poolAllocation: $poolAllocation,
            diversificationScore: $diversificationScore,
            concentrationRisk: $concentrationRisk,
            largestPoolExposure: $largestPoolExposure,
            impermanentRiskEstimate: $impermanentRiskEstimate,
            rewardDependencyRatio: $rewardDepRatio,
            lastActiveAt: $lastActive,
            timestamp: now()->toIso8601String()
        );
    }
}
