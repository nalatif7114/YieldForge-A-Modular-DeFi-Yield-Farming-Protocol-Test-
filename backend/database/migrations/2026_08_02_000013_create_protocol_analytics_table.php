<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocol_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('analytics_version')->default('1.0.0');
            $table->decimal('tvl_daily_change_percentage', 8, 4)->default(0.0);
            $table->decimal('tvl_weekly_change_percentage', 8, 4)->default(0.0);
            $table->decimal('tvl_monthly_change_percentage', 8, 4)->default(0.0);
            $table->integer('active_wallets_count')->default(0);
            $table->integer('new_wallets_count')->default(0);
            $table->integer('returning_wallets_count')->default(0);
            $table->integer('active_pools_count')->default(1);
            $table->unsignedBigInteger('total_transactions_count')->default(0);
            $table->decimal('capital_efficiency_ratio', 8, 4)->default(1.0);
            $table->string('historical_high_tvl_formatted')->default('0');
            $table->string('historical_low_tvl_formatted')->default('0');
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocol_analytics');
    }
};
