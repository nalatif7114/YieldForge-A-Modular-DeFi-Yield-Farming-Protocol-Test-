<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('wallet')->index();
            $table->string('analytics_version')->default('1.0.0');
            $table->string('tvl_raw')->default('0');
            $table->string('tvl_formatted')->default('0');
            $table->string('rewards_raw')->default('0');
            $table->string('rewards_formatted')->default('0');
            $table->string('pending_rewards_raw')->default('0');
            $table->string('pending_rewards_formatted')->default('0');
            $table->decimal('roi_percentage', 8, 4)->default(0.0);
            $table->decimal('apy_percentage', 8, 4)->default(0.0);
            $table->string('compounded_yield_formatted')->default('0');
            $table->json('pool_allocation')->nullable();
            $table->decimal('diversification_score', 8, 4)->default(100.0);
            $table->decimal('concentration_risk', 8, 4)->default(100.0);
            $table->string('largest_pool_exposure')->default('pool-1');
            $table->decimal('impermanent_risk_estimate', 8, 4)->default(0.0);
            $table->decimal('reward_dependency_ratio', 8, 4)->default(0.0);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_analytics');
    }
};
