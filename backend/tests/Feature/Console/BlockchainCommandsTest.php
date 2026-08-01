<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlockchainCommandsTest extends TestCase
{
    use RefreshDatabase;

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
                    return Http::response(['jsonrpc' => '2.0', 'result' => [], 'id' => 1]);
                }

                return Http::response(['jsonrpc' => '2.0', 'result' => '0x0', 'id' => 1]);
            },
        ]);
    }

    public function test_blockchain_sync_command(): void
    {
        $this->artisan('blockchain:sync')
            ->assertExitCode(0);
    }

    public function test_blockchain_replay_command(): void
    {
        $this->artisan('blockchain:replay')
            ->assertExitCode(0);
    }

    public function test_blockchain_verify_command(): void
    {
        $this->artisan('blockchain:verify')
            ->assertExitCode(0);
    }
}
