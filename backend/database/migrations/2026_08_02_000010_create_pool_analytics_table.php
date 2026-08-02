<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('pool_id')->index();
            $table->string('analytics_version')->default('1.0.0');
            $table->string('tvl_raw')->default('0');
            $table->string('tvl_formatted')->default('0');
            $table->integer('active_stakers')->default(0);
            $table->string('average_stake_formatted')->default('0');
            $table->unsignedBigInteger('average_lock_duration')->default(0);
            $table->decimal('average_apy', 8, 4)->default(0.0);
            $table->string('deposit_volume_formatted')->default('0');
            $table->string('withdrawal_volume_formatted')->default('0');
            $table->decimal('utilization_rate', 8, 4)->default(0.0);
            $table->decimal('pool_growth_percentage', 8, 4)->default(0.0);
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_analytics');
    }
};
