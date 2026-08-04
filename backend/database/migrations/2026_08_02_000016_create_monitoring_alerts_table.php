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
        Schema::create('monitoring_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name')->index();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning')->index();
            $table->enum('component', ['indexer', 'rpc', 'queue', 'cache', 'database', 'protocol'])->default('indexer')->index();
            $table->string('message');
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active')->index();
            $table->json('context')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_alerts');
    }
};
