<?php

declare(strict_types=1);

namespace Tests\Unit\Blockchain;

use App\Services\Blockchain\Contracts\AbiLoaderInterface;
use Tests\TestCase;

class AbiLoaderTest extends TestCase
{
    private AbiLoaderInterface $abiLoader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->abiLoader = $this->app->make(AbiLoaderInterface::class);
    }

    public function test_can_load_token_abi(): void
    {
        $abi = $this->abiLoader->getAbi('YieldForgeToken');

        $this->assertIsArray($abi);
        $this->assertNotEmpty($abi);
    }

    public function test_can_find_specific_function_abi(): void
    {
        $functionAbi = $this->abiLoader->getFunctionAbi('YieldForgeToken', 'balanceOf');

        $this->assertNotNull($functionAbi);
        $this->assertEquals('balanceOf', $functionAbi['name']);
        $this->assertEquals('function', $functionAbi['type']);
    }
}
