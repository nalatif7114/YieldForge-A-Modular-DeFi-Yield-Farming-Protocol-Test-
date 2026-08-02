<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\PoolSnapshot;
use App\Services\Analytics\TVLCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TVLCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private TVLCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = $this->app->make(TVLCalculator::class);
    }

    public function test_calculates_current_tvl_formatted(): void
    {
        PoolSnapshot::create([
            'pool_id' => 'pool-1',
            'contract_address' => '0xe7f1725e7734ce288f8367e1bb143e90bb3f0512',
            'staking_token_address' => '0x5fbdb2315678afecb367f032d93f642f64180aa3',
            'total_staked_raw' => '100000000000000000000',
            'total_staked_formatted' => '100.0',
        ]);

        $this->assertEquals('100', $this->calculator->getCurrentTvlFormatted());
    }
}
