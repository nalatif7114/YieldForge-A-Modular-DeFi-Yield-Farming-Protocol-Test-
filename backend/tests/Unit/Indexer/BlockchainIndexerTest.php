<?php

declare(strict_types=1);

namespace Tests\Unit\Indexer;

use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlockchainIndexerTest extends TestCase
{
    use RefreshDatabase;

    private BlockchainIndexerInterface $indexer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->indexer = $this->app->make(BlockchainIndexerInterface::class);
    }

    public function test_sync_range_saves_events_and_updates_cursor(): void
    {
        Http::fake([
            '*' => function (\Illuminate\Http\Client\Request $request) {
                $body = json_decode($request->body(), true);
                $method = $body['method'] ?? '';

                if ($method === 'eth_getLogs') {
                    return Http::response([
                        'jsonrpc' => '2.0',
                        'result' => [
                            [
                                'address' => '0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512',
                                'topics' => [
                                    '0x9e71bc8eea02a63969f509818f2dafb9254532904319f9dbda79b67bd34a5f3d',
                                    '0x000000000000000000000000f39fd6e51aad88f6f4ce6ab8827279cfffb92266',
                                ],
                                'data' => '0x0000000000000000000000000000000000000000000000008782d0d00d400000',
                                'blockNumber' => '0x64',
                                'transactionHash' => '0xabc123',
                                'logIndex' => '0x0',
                            ],
                        ],
                        'id' => 1,
                    ]);
                }

                return Http::response(['jsonrpc' => '2.0', 'result' => '0x64', 'id' => 1]);
            },
        ]);

        $result = $this->indexer->syncRange(100, 100);

        $this->assertEquals(1, $result->blocksProcessed);
        $this->assertEquals(1, $result->eventsIndexed);
        $this->assertDatabaseHas('blockchain_events', [
            'event_name' => 'Staked',
            'transaction_hash' => '0xabc123',
        ]);
        $this->assertDatabaseHas('indexed_blocks', [
            'block_number' => 100,
        ]);
    }
}
