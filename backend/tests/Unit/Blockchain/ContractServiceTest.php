<?php

declare(strict_types=1);

namespace Tests\Unit\Blockchain;

use App\Services\Blockchain\Contracts\ContractServiceInterface;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContractServiceTest extends TestCase
{
    private ContractServiceInterface $contractService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contractService = $this->app->make(ContractServiceInterface::class);
    }

    public function test_get_contracts_returns_configured_list(): void
    {
        $contracts = $this->contractService->getContracts();

        $this->assertIsArray($contracts);
        $this->assertCount(2, $contracts);
        $this->assertEquals('token', $contracts[0]['key']);
        $this->assertEquals('staking', $contracts[1]['key']);
    }

    public function test_get_pools(): void
    {
        Http::fake([
            '*' => Http::response([
                'jsonrpc' => '2.0',
                'result' => '0x0000000000000000000000000000000000000000000000056bc75e2d63100000', // 100 * 10^18 in hex
                'id' => 1,
            ]),
        ]);

        $pools = $this->contractService->getPools();

        $this->assertIsArray($pools);
        $this->assertNotEmpty($pools);
        $this->assertEquals('pool-1', $pools[0]->poolId);
    }

    public function test_get_stake_info(): void
    {
        Http::fake([
            '*' => Http::response([
                'jsonrpc' => '2.0',
                'result' => '0x0000000000000000000000000000000000000000000000008782d0d00d400000', // 9.765625 * 10^18
                'id' => 1,
            ]),
        ]);

        $stake = $this->contractService->getStakeInfo('0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266');

        $this->assertEquals('0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266', $stake->wallet);
        $this->assertNotEmpty($stake->stakedBalanceRaw);
    }
}
