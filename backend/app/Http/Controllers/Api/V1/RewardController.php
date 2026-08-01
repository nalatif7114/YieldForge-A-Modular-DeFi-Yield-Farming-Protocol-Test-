<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RewardResource;
use App\Models\RewardSnapshot;
use App\Models\WalletPosition;
use App\Services\Blockchain\DTO\RewardDTO;

class RewardController extends Controller
{
    public function show(string $wallet): RewardResource
    {
        $walletLower = strtolower($wallet);
        $tokenAddress = (string) config('blockchain.contracts.token.address');

        /** @var WalletPosition|null $position */
        $position = WalletPosition::where('wallet', $walletLower)->first();

        /** @var RewardSnapshot|null $snapshot */
        $snapshot = RewardSnapshot::where('wallet', $walletLower)->first();

        $balanceRaw = $position ? $position->token_balance_raw : ($snapshot ? $snapshot->balance_raw : '0');
        $balanceFormatted = $position ? $position->token_balance_formatted : ($snapshot ? $snapshot->balance_formatted : '0');

        $dto = new RewardDTO(
            wallet: $wallet,
            tokenAddress: $tokenAddress,
            tokenSymbol: 'YFT',
            balanceRaw: $balanceRaw,
            balanceFormatted: $balanceFormatted,
            pendingRewardsRaw: $snapshot ? $snapshot->pending_rewards_raw : '0',
            pendingRewardsFormatted: $snapshot ? $snapshot->pending_rewards_formatted : '0'
        );

        return new RewardResource($dto);
    }
}
