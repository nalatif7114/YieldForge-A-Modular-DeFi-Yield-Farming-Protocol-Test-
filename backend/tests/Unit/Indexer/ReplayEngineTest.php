<?php

declare(strict_types=1);

namespace Tests\Unit\Indexer;

use App\Models\BlockchainEvent;
use App\Services\Indexer\Contracts\ReplayEngineInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplayEngineTest extends TestCase
{
    use RefreshDatabase;

    private ReplayEngineInterface $replayEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->replayEngine = $this->app->make(ReplayEngineInterface::class);
    }

    public function test_replays_events_from_event_store(): void
    {
        BlockchainEvent::create([
            'chain_id' => 11155111,
            'network' => 'sepolia',
            'block_number' => 100,
            'transaction_hash' => '0xreplaytx1',
            'log_index' => 0,
            'contract_address' => '0xe7f1725e7734ce288f8367e1bb143e90bb3f0512',
            'event_name' => 'Staked',
            'payload' => [
                'user' => '0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266',
                'amount_raw' => '1000000000000000000',
                'amount_formatted' => '1.0',
            ],
            'removed' => false,
        ]);

        $count = $this->replayEngine->replay(0);

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('wallet_positions', [
            'wallet' => '0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266',
            'staked_balance_raw' => '1000000000000000000',
        ]);
    }
}
