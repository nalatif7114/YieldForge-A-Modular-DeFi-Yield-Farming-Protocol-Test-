<?php

declare(strict_types=1);

namespace Tests\Unit\Blockchain;

use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NetworkServiceTest extends TestCase
{
    private NetworkServiceInterface $networkService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->networkService = $this->app->make(NetworkServiceInterface::class);
    }

    public function test_get_network_info(): void
    {
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

                return Http::response(['jsonrpc' => '2.0', 'result' => null, 'id' => 1]);
            },
        ]);

        $dto = $this->networkService->getNetworkInfo();

        $this->assertEquals(11155111, $dto->chainId);
        $this->assertEquals(100, $dto->blockNumber);
        $this->assertTrue($dto->isConnected);
    }

    public function test_is_healthy(): void
    {
        Http::fake([
            '*' => function (\Illuminate\Http\Client\Request $request) {
                $body = json_decode($request->body(), true);
                $method = $body['method'] ?? '';

                if ($method === 'eth_chainId') {
                    return Http::response(['jsonrpc' => '2.0', 'result' => '0xaa36a7', 'id' => 1]);
                }

                return Http::response(['jsonrpc' => '2.0', 'result' => '0x10', 'id' => 1]);
            },
        ]);

        $this->assertTrue($this->networkService->isHealthy());
    }
}
