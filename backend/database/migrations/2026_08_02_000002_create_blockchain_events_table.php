<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchain_events', function (Blueprint $table): void {
            $table->id();
            $table->integer('chain_id')->default(11155111);
            $table->string('network')->default('sepolia');
            $table->unsignedBigInteger('block_number');
            $table->string('block_hash')->nullable();
            $table->string('transaction_hash');
            $table->integer('transaction_index')->default(0);
            $table->integer('log_index');
            $table->string('contract_address');
            $table->string('event_name');
            $table->string('event_version')->default('1.0.0');
            $table->string('contract_version')->default('1.0.0');
            $table->json('payload');
            $table->boolean('removed')->default(false);
            $table->timestamp('timestamp')->nullable();
            $table->timestamps();

            $table->unique(['transaction_hash', 'log_index']);
            $table->index(['block_number', 'log_index']);
            $table->index(['event_name', 'block_number']);
            $table->index('contract_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_events');
    }
};
