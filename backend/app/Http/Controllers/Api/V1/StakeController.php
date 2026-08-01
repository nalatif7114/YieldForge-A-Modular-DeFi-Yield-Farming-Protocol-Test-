<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StakeResource;
use App\Models\WalletPosition;
use App\Services\Blockchain\DTO\StakeDTO;

class StakeController extends Controller
{
    public function show(string $wallet): StakeResource
    {
        $walletLower = strtolower($wallet);
        /** @var WalletPosition|null $position */
        $position = WalletPosition::where('wallet', $walletLower)->first();

        $stakingAddress = (string) config('blockchain.contracts.staking.address');

        $dto = new StakeDTO(
            wallet: $wallet,
            stakingContract: $stakingAddress,
            stakedBalanceRaw: $position ? $position->staked_balance_raw : '0',
            stakedBalanceFormatted: $position ? $position->staked_balance_formatted : '0',
            poolSharePercentage: $position ? $position->pool_share_percentage : 0.0
        );

        return new StakeResource($dto);
    }
}
