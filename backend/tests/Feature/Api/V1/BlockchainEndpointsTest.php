<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlockchainEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => function (\Illuminate\Http\Client\Request $request) {
                $body = json_decode($request->body(), true);
                $method = $body['method'] ?? '';

                if ($method === 'eth_chainId') {
                    return Http::response(['jsonrpc' => '2.0', 'result' => '0xaa36a7', 'id' => 1]);
                }

                if ($method === 'eth_blockNumber') {
                    return Http::response(['jsonrpc' => '2.0', 'result' => '0x64', 'id' => 1]);
                }

                if ($method === 'eth_getLogs') {
                    return Http::response([
                        'jsonrpc' => '2.0',
                        'result' => [
                            [
                                'address' => '0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512',
                                'topics' => ['0x9e71bc8eea02a63969f509818f2daeb92bc7264d97b1cd0ef59b30ea9b427dae'],
                                'data' => '0x0000000000000000000000000000000000000000000000008782d0d00d400000',
                                'blockNumber' => '0x64',
                                'transactionHash' => '0xabc123',
                                'logIndex' => '0x0',
                            ],
                        ],
                        'id' => 1,
                    ]);
                }

                return Http::response([
                    'jsonrpc' => '2.0',
                    'result' => '0x0000000000000000000000000000000000000000000000056bc75e2d63100000',
                    'id' => 1,
                ]);
            },
        ]);
    }

    public function test_get_network_endpoint(): void
    {
        $response = $this->getJson('/api/v1/network');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'chain_id',
                    'network_name',
                    'block_number',
                    'rpc_url',
                    'is_connected',
                    'latency_ms',
                ],
            ]);
    }

    public function test_get_contracts_endpoint(): void
    {
        $response = $this->getJson('/api/v1/contracts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['key', 'name', 'type', 'address', 'abi_file'],
                ],
            ]);
    }

    public function test_get_pools_endpoint(): void
    {
        $response = $this->getJson('/api/v1/pools');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'pool_id',
                        'contract_address',
                        'staking_token',
                        'total_staked_raw',
                        'total_staked_formatted',
                        'is_paused',
                    ],
                ],
            ]);
    }

    public function test_get_stake_by_wallet_endpoint(): void
    {
        $wallet = '0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266';
        $response = $this->getJson("/api/v1/stakes/{$wallet}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'wallet',
                    'staking_contract',
                    'staked_balance_raw',
                    'staked_balance_formatted',
                    'pool_share_percentage',
                ],
            ]);
    }

    public function test_get_rewards_by_wallet_endpoint(): void
    {
        $wallet = '0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266';
        $response = $this->getJson("/api/v1/rewards/{$wallet}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'wallet',
                    'token_address',
                    'token_symbol',
                    'balance_raw',
                    'balance_formatted',
                    'pending_rewards_raw',
                    'pending_rewards_formatted',
                ],
            ]);
    }

    public function test_get_events_endpoint(): void
    {
        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'event_name',
                        'contract_address',
                        'transaction_hash',
                        'block_number',
                        'log_index',
                        'parameters',
                    ],
                ],
            ]);
    }

    public function test_health_endpoint(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'services' => [
                    'api',
                    'blockchain' => [
                        'connected',
                        'chain_id',
                        'block_number',
                        'latency_ms',
                    ],
                ],
            ]);
    }
}
