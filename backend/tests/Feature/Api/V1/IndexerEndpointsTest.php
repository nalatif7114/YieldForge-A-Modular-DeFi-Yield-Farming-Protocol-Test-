<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\BlockchainEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexerEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response(['jsonrpc' => '2.0', 'result' => '0xaa36a7', 'id' => 1]),
        ]);
    }

    public function test_get_indexer_status(): void
    {
        $response = $this->getJson('/api/v1/indexer');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'network',
                    'latest_block',
                    'indexed_block',
                    'lag',
                    'events',
                    'status',
                    'last_sync',
                    'projection_version',
                ],
            ]);
    }

    public function test_get_indexer_metrics(): void
    {
        $response = $this->getJson('/api/v1/indexer/metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'events_per_sec',
                    'blocks_per_sec',
                    'avg_rpc_latency_ms',
                    'projection_latency_ms',
                    'queue_latency_ms',
                    'replay_duration_ms',
                    'cache_hit_ratio',
                    'retry_count',
                    'failed_projections',
                ],
            ]);
    }

    public function test_get_stats_endpoint(): void
    {
        $response = $this->getJson('/api/v1/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_value_locked_raw',
                    'total_value_locked_formatted',
                    'total_stakers_count',
                    'total_events_processed',
                    'total_tokens_minted_raw',
                    'total_tokens_burned_raw',
                    'latest_indexed_block',
                ],
            ]);
    }
}
