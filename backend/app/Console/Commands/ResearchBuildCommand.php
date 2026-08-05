<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Research\FeatureStoreService;
use App\Services\Research\ResearchDatasetEngine;
use Illuminate\Console\Command;

class ResearchBuildCommand extends Command
{
    protected $signature = 'research:build {--type=all} {--ver=1.0.0}';

    protected $description = 'Build curated research datasets and compute ML features';

    public function handle(ResearchDatasetEngine $datasetEngine, FeatureStoreService $featureStore): int
    {
        $type = (string) $this->option('type');
        $version = (string) $this->option('ver');

        $this->info("Building YieldForge Research Datasets (Version {$version})...");

        // 1. Register default Feature Sets definition
        $featureStore->registerFeatureSet(
            name: 'wallet_ml_features_v1',
            version: $version,
            featureNames: [
                'wallet_age_days',
                'average_stake_formatted',
                'staking_frequency',
                'holding_duration_days',
                'reward_velocity',
                'stake_growth_pct',
                'unstake_ratio',
                'active_days',
                'transaction_interval_hours',
                'pool_diversity_count',
            ],
            metadata: ['target_domain' => 'staking_behavior_clustering']
        );

        // 2. Build Datasets
        if ($type === 'all') {
            $datasets = $datasetEngine->buildAllDatasets();
            foreach ($datasets as $name => $ds) {
                $this->line(" - Dataset [{$name}]: Rows: {$ds->row_count}, Quality Score: {$ds->quality_score}/100");
            }
        } else {
            $ds = $datasetEngine->buildDataset($type, $version);
            $this->line(" - Dataset [{$type}]: Rows: {$ds->row_count}, Quality Score: {$ds->quality_score}/100");
        }

        $this->info('STATUS: Research Datasets and Feature Sets built successfully.');

        return Command::SUCCESS;
    }
}
