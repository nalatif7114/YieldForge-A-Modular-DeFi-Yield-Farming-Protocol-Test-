<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use App\Models\TransactionHistory;
use App\Models\WalletPosition;
use App\Services\Research\Support\FeatureCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_wallet_features_for_empty_wallet(): void
    {
        $wallet = '0x1111111111111111111111111111111111111111';
        $features = FeatureCalculator::calculateWalletFeatures($wallet);

        $this->assertIsArray($features);
        $this->assertEquals(strtolower($wallet), $features['wallet_address']);
        $this->assertEquals(0, $features['wallet_age_days']);
        $this->assertEquals('0', $features['average_stake_formatted']);
    }

    public function test_calculate_wallet_features_with_history(): void
    {
        $wallet = '0x86b6346984f6f9380a94bc0d2c006044649f2077';

        WalletPosition::create([
            'wallet' => $wallet,
            'staked_balance_raw' => '100000000000000000000',
            'staked_balance_formatted' => '100.0',
            'pool_id' => 1,
        ]);

        TransactionHistory::create([
            'transaction_hash' => '0x' . str_repeat('a', 64),
            'wallet' => $wallet,
            'event_name' => 'Staked',
            'amount_raw' => '100000000000000000000',
            'amount_formatted' => '100.0',
            'pool_id' => 1,
            'block_number' => 11415659,
            'timestamp' => now()->subDays(5),
        ]);

        $features = FeatureCalculator::calculateWalletFeatures($wallet);

        $this->assertIsArray($features);
        $this->assertEquals(strtolower($wallet), $features['wallet_address']);
        $this->assertEquals('100.0', $features['average_stake_formatted']);
        $this->assertGreaterThan(0, $features['wallet_age_days']);
        $this->assertGreaterThan(0, $features['holding_duration_days']);
        $this->assertGreaterThanOrEqual(0, $features['active_days']);
        $this->assertGreaterThanOrEqual(0.0, $features['staking_frequency']);
        $this->assertGreaterThanOrEqual(0.0, $features['unstake_ratio']);
        $this->assertGreaterThanOrEqual(0.0, $features['reward_velocity']);
        $this->assertGreaterThanOrEqual(0.0, $features['stake_growth_pct']);
        $this->assertEquals(1, $features['pool_diversity_count']);
    }
}
