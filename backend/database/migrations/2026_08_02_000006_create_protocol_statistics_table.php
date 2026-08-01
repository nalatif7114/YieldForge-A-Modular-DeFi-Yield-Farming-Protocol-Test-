<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocol_statistics', function (Blueprint $table): void {
            $table->id();
            $table->string('total_value_locked_raw')->default('0');
            $table->string('total_value_locked_formatted')->default('0');
            $table->integer('total_stakers_count')->default(0);
            $table->unsignedBigInteger('total_events_processed')->default(0);
            $table->string('total_tokens_minted_raw')->default('0');
            $table->string('total_tokens_burned_raw')->default('0');
            $table->unsignedBigInteger('latest_indexed_block')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocol_statistics');
    }
};
