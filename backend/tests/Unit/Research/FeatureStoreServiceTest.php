<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use App\Models\FeatureSet;
use App\Models\WalletFeature;
use App\Services\Research\FeatureStoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureStoreServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeatureStoreService $featureStore;

    protected function setUp(): void
    {
        parent::setUp();
        $this->featureStore = $this->app->make(FeatureStoreService::class);
    }

    public function test_register_feature_set_creates_feature_set_record(): void
    {
        $fs = $this->featureStore->registerFeatureSet(
            name: 'wallet_v1',
            version: '1.0.0',
            featureNames: ['wallet_age_days', 'average_stake_formatted'],
            metadata: ['domain' => 'research']
        );

        $this->assertInstanceOf(FeatureSet::class, $fs);
        $this->assertEquals('wallet_v1', $fs->name);
        $this->assertEquals(2, $fs->feature_count);
        $this->assertDatabaseHas('feature_sets', ['name' => 'wallet_v1']);
    }

    public function test_compute_wallet_feature_vector(): void
    {
        $wallet = '0x86b6346984f6f9380a94bc0d2c006044649f2077';
        $wf = $this->featureStore->computeWalletFeatureVector($wallet);

        $this->assertInstanceOf(WalletFeature::class, $wf);
        $this->assertEquals(strtolower($wallet), $wf->wallet_address);
        $this->assertDatabaseHas('wallet_features', ['wallet_address' => strtolower($wallet)]);
    }
}
