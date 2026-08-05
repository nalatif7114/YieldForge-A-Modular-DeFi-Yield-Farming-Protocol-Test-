<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Models\WalletFeature;
use App\Models\WalletPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletResearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 50);

        $features = WalletFeature::query()
            ->orderByDesc('average_stake_formatted')
            ->limit($limit)
            ->get();

        if ($features->isEmpty()) {
            $features = WalletPosition::query()
                ->orderByDesc('staked_balance_raw')
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_wallets' => WalletPosition::count(),
                'wallets' => $features,
            ],
        ]);
    }
}
