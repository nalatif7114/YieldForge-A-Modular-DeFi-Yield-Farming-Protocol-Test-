<?php

declare(strict_types=1);

namespace Tests\Unit\Blockchain;

use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\Exceptions\RpcException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RpcClientTest extends TestCase
{
    private RpcClientInterface $rpcClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rpcClient = $this->app->make(RpcClientInterface::class);
    }

    public function test_successful_single_rpc_call(): void
    {
        Http::fake([
            '*' => Http::response([
                'jsonrpc' => '2.0',
                'result' => '0xaa36a7',
                'id' => 1,
            ], 200),
        ]);

        $result = $this->rpcClient->call('eth_chainId');

        $this->assertEquals('0xaa36a7', $result);
    }

    public function test_rpc_error_response_throws_rpc_exception(): void
    {
        Http::fake([
            '*' => Http::response([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32601,
                    'message' => 'Method not found',
                ],
                'id' => 1,
            ], 200),
        ]);

        $this->expectException(RpcException::class);
        $this->expectExceptionMessage('Method not found');

        $this->rpcClient->call('invalid_method');
    }

    public function test_http_retry_on_transient_failure(): void
    {
        Http::fakeSequence()
            ->push('Server Error', 503)
            ->push([
                'jsonrpc' => '2.0',
                'result' => '0x123',
                'id' => 1,
            ], 200);

        $result = $this->rpcClient->call('eth_blockNumber');

        $this->assertEquals('0x123', $result);
    }
}
