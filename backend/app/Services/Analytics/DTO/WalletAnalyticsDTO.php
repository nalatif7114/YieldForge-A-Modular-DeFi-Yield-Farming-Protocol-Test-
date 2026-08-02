<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class WalletAnalyticsDTO
{
    public function __construct(
        public string $wallet,
        public string $tvlRaw,
        public string $tvlFormatted,
        public string $rewardsRaw,
        public string $rewardsFormatted,
        public string $pendingRewardsRaw,
        public string $pendingRewardsFormatted,
        public float $roiPercentage,
        public float $apyPercentage,
        public string $compoundedYieldFormatted,
        public array $poolAllocation,
        public float $diversificationScore,
        public float $concentrationRisk,
        public string $largestPoolExposure,
        public float $impermanentRiskEstimate,
        public float $rewardDependencyRatio,
        public ?string $lastActiveAt,
        public string $timestamp
    ) {}

    public function toArray(): array
    {
        return [
            'wallet' => $this->wallet,
            'tvl_raw' => $this->tvlRaw,
            'tvl_formatted' => $this->tvlFormatted,
            'rewards_raw' => $this->rewardsRaw,
            'rewards_formatted' => $this->rewardsFormatted,
            'pending_rewards_raw' => $this->pendingRewardsRaw,
            'pending_rewards_formatted' => $this->pendingRewardsFormatted,
            'roi_percentage' => $this->roiPercentage,
            'apy_percentage' => $this->apyPercentage,
            'compounded_yield_formatted' => $this->compoundedYieldFormatted,
            'pool_allocation' => $this->poolAllocation,
            'risk_metrics' => [
                'diversification_score' => $this->diversificationScore,
                'concentration_risk' => $this->concentrationRisk,
                'largest_pool_exposure' => $this->largestPoolExposure,
                'impermanent_risk_estimate' => $this->impermanentRiskEstimate,
                'reward_dependency_ratio' => $this->rewardDependencyRatio,
            ],
            'last_active_at' => $this->lastActiveAt,
            'timestamp' => $this->timestamp,
        ];
    }
}
