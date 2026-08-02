<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Models\WalletPosition;
use App\Services\Analytics\PortfolioAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    private PortfolioAnalyzer $analyzer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyzer = $this->app->make(PortfolioAnalyzer::class);
    }

    public function test_analyzes_wallet_portfolio(): void
    {
        $wallet = '0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266';
        WalletPosition::create([
            'wallet' => $wallet,
            'staked_balance_raw' => '5000000000000000000',
            'staked_balance_formatted' => '5.0',
            'token_balance_raw' => '1000000000000000000',
            'token_balance_formatted' => '1.0',
            'pool_share_percentage' => 50.0,
        ]);

        $dto = $this->analyzer->analyze($wallet);

        $this->assertEquals($wallet, $dto->wallet);
        $this->assertEquals('5.0', $dto->tvlFormatted);
        $this->assertEquals(20.0, $dto->roiPercentage);
        $this->assertEquals(100.0, $dto->concentrationRisk);
    }
}
