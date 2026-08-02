<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\HourlyStatistic;
use App\Models\PoolSnapshot;
use App\Services\Analytics\APYCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class APYCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private APYCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = $this->app->make(APYCalculator::class);
    }

    public function test_calculates_current_and_average_apy(): void
    {
        PoolSnapshot::create([
            'pool_id' => 'pool-1',
            'contract_address' => '0xe7f1725e7734ce288f8367e1bb143e90bb3f0512',
            'staking_token_address' => '0x5fbdb2315678afecb367f032d93f642f64180aa3',
            'total_staked_raw' => '1000000000000000000',
        ]);

        HourlyStatistic::create([
            'timestamp' => now()->subHour(),
            'apy' => 14.50,
        ]);

        $this->assertEquals(12.50, $this->calculator->getCurrentApy('pool-1'));
        $this->assertEquals(14.50, $this->calculator->getAverageApy(24));
    }
}
