<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('analytics_version')->default('1.0.0');
            $table->string('total_rewards_distributed_raw')->default('0');
            $table->string('total_rewards_distributed_formatted')->default('0');
            $table->decimal('reward_velocity', 12, 4)->default(0.0);
            $table->json('top_earners')->nullable();
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_analytics');
    }
};
