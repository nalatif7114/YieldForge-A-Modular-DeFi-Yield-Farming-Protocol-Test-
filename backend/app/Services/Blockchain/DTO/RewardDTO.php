<?php

declare(strict_types=1);

namespace App\Services\Blockchain\DTO;

readonly class RewardDTO
{
    public function __construct(
        public string $wallet,
        public string $tokenAddress,
        public string $tokenSymbol,
        public string $balanceRaw,
        public string $balanceFormatted,
        public string $pendingRewardsRaw,
        public string $pendingRewardsFormatted
    ) {}

    /**
     * Convert DTO to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'wallet' => $this->wallet,
            'token_address' => $this->tokenAddress,
            'token_symbol' => $this->tokenSymbol,
            'balance_raw' => $this->balanceRaw,
            'balance_formatted' => $this->balanceFormatted,
            'pending_rewards_raw' => $this->pendingRewardsRaw,
            'pending_rewards_formatted' => $this->pendingRewardsFormatted,
        ];
    }
}
