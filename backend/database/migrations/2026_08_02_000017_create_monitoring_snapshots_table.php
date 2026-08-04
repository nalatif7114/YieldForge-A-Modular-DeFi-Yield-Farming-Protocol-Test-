<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitoring_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('health_score')->default(100)->index();
            $table->unsignedBigInteger('indexer_lag')->default(0);
            $table->float('rpc_latency_ms')->default(0.0);
            $table->unsignedInteger('queue_pending_jobs')->default(0);
            $table->unsignedInteger('queue_failed_jobs')->default(0);
            $table->float('cache_hit_ratio')->default(100.0);
            $table->unsignedInteger('active_alerts_count')->default(0);
            $table->json('metrics')->nullable();
            $table->timestamp('timestamp')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_snapshots');
    }
};
