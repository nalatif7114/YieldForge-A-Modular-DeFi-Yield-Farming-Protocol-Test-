<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Services\Analytics\YieldCalculator;
use Tests\TestCase;

class YieldCalculatorTest extends TestCase
{
    private YieldCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = $this->app->make(YieldCalculator::class);
    }

    public function test_calculates_roi_and_compounded_yield(): void
    {
        $roi = $this->calculator->calculateRoi('10000000000000000000', '1000000000000000000');
        $this->assertEquals(10.0, $roi);

        $yield = $this->calculator->calculateCompoundedYield('100000000000000000000', 10.0, 365);
        $this->assertGreaterThan(10.0, (float) $yield);
    }
}
