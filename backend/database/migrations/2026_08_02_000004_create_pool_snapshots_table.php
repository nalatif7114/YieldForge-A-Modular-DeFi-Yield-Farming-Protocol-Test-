<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('pool_id')->unique();
            $table->string('contract_address');
            $table->string('staking_token_address');
            $table->string('staking_token_name')->default('YieldForge Token');
            $table->string('staking_token_symbol')->default('YFT');
            $table->integer('staking_token_decimals')->default(18);
            $table->string('total_staked_raw')->default('0');
            $table->string('total_staked_formatted')->default('0');
            $table->boolean('is_paused')->default(false);
            $table->unsignedBigInteger('block_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_snapshots');
    }
};
