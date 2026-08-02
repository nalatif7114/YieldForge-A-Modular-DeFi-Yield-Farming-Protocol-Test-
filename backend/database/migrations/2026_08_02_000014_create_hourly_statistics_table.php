<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hourly_statistics', function (Blueprint $table): void {
            $table->id();
            $table->string('analytics_version')->default('1.0.0');
            $table->timestamp('timestamp')->index();
            $table->string('tvl_formatted')->default('0');
            $table->decimal('apy', 8, 4)->default(0.0);
            $table->string('volume_formatted')->default('0');
            $table->integer('tx_count')->default(0);
            $table->integer('active_users')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hourly_statistics');
    }
};
