<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projection_checkpoints', function (Blueprint $table): void {
            $table->id();
            $table->string('projection_name')->unique();
            $table->unsignedBigInteger('last_processed_block')->default(0);
            $table->integer('last_transaction_index')->default(0);
            $table->integer('last_log_index')->default(0);
            $table->string('projection_version')->default('1.0.0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projection_checkpoints');
    }
};
