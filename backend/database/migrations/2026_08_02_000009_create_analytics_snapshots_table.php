<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->integer('chain_id')->default(11155111);
            $table->string('network')->default('sepolia');
            $table->string('snapshot_type')->default('5m');
            $table->string('analytics_version')->default('1.0.0');
            $table->string('total_tvl_raw')->default('0');
            $table->string('total_tvl_formatted')->default('0');
            $table->decimal('average_apy', 8, 4)->default(0.0);
            $table->integer('active_stakers')->default(0);
            $table->string('total_rewards_raw')->default('0');
            $table->string('metric_name')->nullable()->index();
            $table->double('metric_value')->nullable();
            $table->string('aggregation_window')->default('5m');
            $table->string('source')->default('indexer_engine');
            $table->json('metadata')->nullable();
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
    }
};
