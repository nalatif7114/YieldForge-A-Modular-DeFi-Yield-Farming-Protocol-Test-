<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PoolResource;
use App\Models\PoolSnapshot;
use App\Services\Blockchain\DTO\PoolDTO;

class PoolController extends Controller
{
    public function index()
    {
        $pools = PoolSnapshot::all()->map(function (PoolSnapshot $snapshot) {
            return new PoolDTO(
                poolId: $snapshot->pool_id,
                contractAddress: $snapshot->contract_address,
                stakingTokenAddress: $snapshot->staking_token_address,
                stakingTokenName: $snapshot->staking_token_name,
                stakingTokenSymbol: $snapshot->staking_token_symbol,
                stakingTokenDecimals: $snapshot->staking_token_decimals,
                totalStakedRaw: $snapshot->total_staked_raw,
                totalStakedFormatted: $snapshot->total_staked_formatted,
                isPaused: $snapshot->is_paused
            );
        });

        if ($pools->isEmpty()) {
            // Default placeholder snapshot if indexing has not populated yet
            $pools = collect([
                new PoolDTO(
                    poolId: 'pool-1',
                    contractAddress: (string) config('blockchain.contracts.staking.address'),
                    stakingTokenAddress: (string) config('blockchain.contracts.token.address'),
                    stakingTokenName: 'YieldForge Token',
                    stakingTokenSymbol: 'YFT',
                    stakingTokenDecimals: 18,
                    totalStakedRaw: '0',
                    totalStakedFormatted: '0',
                    isPaused: false
                )
            ]);
        }

        return PoolResource::collection($pools);
    }
}
