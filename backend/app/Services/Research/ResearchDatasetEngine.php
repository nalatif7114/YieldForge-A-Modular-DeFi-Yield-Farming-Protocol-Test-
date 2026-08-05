<?php

declare(strict_types=1);

namespace App\Services\Research;

use App\Models\BlockchainEvent;
use App\Models\DatasetVersion;
use App\Models\ResearchDataset;
use App\Models\WalletPosition;
use Illuminate\Database\Eloquent\Collection;

class ResearchDatasetEngine
{
    public function __construct(
        private readonly DataQualityEngine $qualityEngine,
        private readonly FeatureStoreService $featureStoreService
    ) {}

    /**
     * Build curated research dataset for ML/Research consumption.
     *
     * @param string $type
     * @param string $version
     * @return ResearchDataset
     */
    public function buildDataset(string $type, string $version = '1.0.0'): ResearchDataset
    {
        $quality = $this->qualityEngine->validateDataset($type);

        $rowCount = match ($type) {
            'wallet_behavior' => WalletPosition::count(),
            'transaction_features', 'staking_history' => BlockchainEvent::count(),
            default => max(1, BlockchainEvent::count()),
        };

        // If wallet_behavior, trigger feature computation for existing wallets
        if ($type === 'wallet_behavior') {
            /** @var Collection<int, WalletPosition> $positions */
            $positions = WalletPosition::all();
            foreach ($positions as $pos) {
                $this->featureStoreService->computeWalletFeatureVector($pos->wallet, $version);
            }
        }

        /** @var ResearchDataset $dataset */
        $dataset = ResearchDataset::updateOrCreate(
            [
                'name' => "Dataset_{$type}",
                'type' => $type,
            ],
            [
                'version' => $version,
                'row_count' => $rowCount,
                'quality_score' => $quality['quality_score'],
                'status' => 'ready',
            ]
        );

        DatasetVersion::create([
            'dataset_id' => $dataset->id,
            'version' => $version,
            'checksum' => md5("{$dataset->name}_{$version}_{$rowCount}"),
            'file_path' => "research/datasets/{$type}_v{$version}.json",
            'row_count' => $rowCount,
        ]);

        return $dataset;
    }

    /**
     * Build all 6 curated research datasets.
     *
     * @return array<string, ResearchDataset>
     */
    public function buildAllDatasets(): array
    {
        $types = [
            'wallet_behavior',
            'pool_activity',
            'reward_distribution',
            'protocol_growth',
            'staking_history',
            'transaction_features',
        ];

        $results = [];
        foreach ($types as $t) {
            $results[$t] = $this->buildDataset($t);
        }

        return $results;
    }
}
