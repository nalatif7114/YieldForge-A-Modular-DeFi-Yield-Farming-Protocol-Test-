<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Research\FeatureStoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateWalletFeaturesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $walletAddress,
        public string $version = '1.0.0'
    ) {}

    public function handle(FeatureStoreService $featureStore): void
    {
        $featureStore->computeWalletFeatureVector($this->walletAddress, $this->version);
    }
}
