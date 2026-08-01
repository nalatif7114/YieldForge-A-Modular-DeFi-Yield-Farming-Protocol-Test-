<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indexed_blocks', function (Blueprint $table): void {
            $table->id();
            $table->integer('chain_id')->default(11155111);
            $table->string('network')->default('sepolia');
            $table->unsignedBigInteger('block_number')->unique();
            $table->string('block_hash')->nullable();
            $table->string('parent_hash')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('status')->default('processed');
            $table->integer('events_count')->default(0);
            $table->timestamps();

            $table->index(['chain_id', 'block_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indexed_blocks');
    }
};
