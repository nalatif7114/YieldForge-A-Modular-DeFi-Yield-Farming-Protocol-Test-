<?php

declare(strict_types=1);

namespace App\Services\Research;

use App\Models\FeatureSet;
use App\Models\PoolFeature;
use App\Models\WalletFeature;
use App\Services\Research\Support\FeatureCalculator;
use Illuminate\Database\Eloquent\Collection;

class FeatureStoreService
{
    /**
     * Register or update a versioned feature set definition in feature_sets store.
     *
     * @param string $name
     * @param string $version
     * @param array $featureNames
     * @param array $metadata
     * @return FeatureSet
     */
    public function registerFeatureSet(
        string $name,
        string $version = '1.0.0',
        array $featureNames = [],
        array $metadata = []
    ): FeatureSet {
        /** @var FeatureSet $featureSet */
        $featureSet = FeatureSet::updateOrCreate(
            [
                'name' => $name,
                'version' => $version,
            ],
            [
                'feature_count' => count($featureNames),
                'metadata' => array_merge(['features' => $featureNames], $metadata),
            ]
        );

        return $featureSet;
    }

    /**
     * Compute and update feature vector for a wallet address.
     *
     * @param string $walletAddress
     * @param string $version
     * @return WalletFeature
     */
    public function computeWalletFeatureVector(string $walletAddress, string $version = '1.0.0'): WalletFeature
    {
        $features = FeatureCalculator::calculateWalletFeatures($walletAddress);
        $features['feature_version'] = $version;

        /** @var WalletFeature $wf */
        $wf = WalletFeature::updateOrCreate(
            ['wallet_address' => strtolower(trim($walletAddress))],
            $features
        );

        return $wf;
    }

    /**
     * Get feature sets list.
     *
     * @return Collection<int, FeatureSet>
     */
    public function getFeatureSets(): Collection
    {
        return FeatureSet::query()->orderByDesc('created_at')->get();
    }

    /**
     * Get wallet features.
     *
     * @param string|null $walletAddress
     * @return Collection<int, WalletFeature>
     */
    public function getWalletFeatures(?string $walletAddress = null): Collection
    {
        $query = WalletFeature::query()->orderByDesc('updated_at');
        if ($walletAddress) {
            $query->where('wallet_address', strtolower(trim($walletAddress)));
        }

        return $query->get();
    }

    /**
     * Get pool features.
     *
     * @param int|null $poolId
     * @return Collection<int, PoolFeature>
     */
    public function getPoolFeatures(?int $poolId = null): Collection
    {
        $query = PoolFeature::query()->orderByDesc('updated_at');
        if ($poolId !== null) {
            $query->where('pool_id', $poolId);
        }

        return $query->get();
    }
}
