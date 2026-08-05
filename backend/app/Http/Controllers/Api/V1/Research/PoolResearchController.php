<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Models\PoolFeature;
use App\Models\PoolSnapshot;
use Illuminate\Http\JsonResponse;

class PoolResearchController extends Controller
{
    public function index(): JsonResponse
    {
        $features = PoolFeature::all();
        $snapshots = PoolSnapshot::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_pools_count' => $snapshots->count(),
                'pool_features' => $features,
                'pool_snapshots' => $snapshots,
            ],
        ]);
    }
}
