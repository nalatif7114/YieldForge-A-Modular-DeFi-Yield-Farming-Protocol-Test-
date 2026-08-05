<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Services\Research\FeatureStoreService;
use Illuminate\Http\JsonResponse;

class FeatureResearchController extends Controller
{
    public function index(FeatureStoreService $featureStore): JsonResponse
    {
        $featureSets = $featureStore->getFeatureSets();
        $walletFeatures = $featureStore->getWalletFeatures();
        $poolFeatures = $featureStore->getPoolFeatures();

        return response()->json([
            'status' => 'success',
            'data' => [
                'feature_sets' => $featureSets,
                'wallet_features_sample' => $walletFeatures->take(10),
                'pool_features_sample' => $poolFeatures->take(10),
            ],
        ]);
    }
}
