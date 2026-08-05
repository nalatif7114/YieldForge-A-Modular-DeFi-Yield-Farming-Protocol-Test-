<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use App\Models\BlockchainEvent;
use App\Services\Research\DataQualityEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataQualityEngineTest extends TestCase
{
    use RefreshDatabase;

    private DataQualityEngine $qualityEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qualityEngine = $this->app->make(DataQualityEngine::class);
    }

    public function test_validate_clean_dataset_passes(): void
    {
        BlockchainEvent::create([
            'transaction_hash' => '0x' . str_repeat('b', 64),
            'log_index' => 0,
            'contract_address' => '0x' . str_repeat('c', 40),
            'event_name' => 'Staked',
            'topic_hash' => '0x' . str_repeat('d', 64),
            'block_number' => 100,
            'block_hash' => '0x' . str_repeat('e', 64),
            'payload' => [],
            'decoded_payload' => [],
            'timestamp' => now()->subMinute(),
        ]);

        $res = $this->qualityEngine->validateDataset('wallet_behavior');

        $this->assertIsArray($res);
        $this->assertEquals(100, $res['quality_score']);
        $this->assertEquals('passed', $res['status']);
        $this->assertEquals(0, $res['issues_count']);
        $this->assertTrue($res['checks']['missing_values']['passed']);
        $this->assertTrue($res['checks']['duplicate_events']['passed']);
        $this->assertTrue($res['checks']['timestamp_validity']['passed']);
        $this->assertTrue($res['checks']['outlier_detection']['passed']);
        $this->assertTrue($res['checks']['completeness']['passed']);
    }

    public function test_validate_dataset_detects_future_timestamp_issue(): void
    {
        BlockchainEvent::create([
            'transaction_hash' => '0x' . str_repeat('f', 64),
            'log_index' => 1,
            'contract_address' => '0x' . str_repeat('c', 40),
            'event_name' => 'Staked',
            'topic_hash' => '0x' . str_repeat('d', 64),
            'block_number' => 101,
            'block_hash' => '0x' . str_repeat('e', 64),
            'payload' => [],
            'decoded_payload' => [],
            'timestamp' => now()->addDays(5),
        ]);

        $res = $this->qualityEngine->validateDataset('wallet_behavior');

        $this->assertLessThan(100, $res['quality_score']);
        $this->assertFalse($res['checks']['timestamp_validity']['passed']);
    }
}
