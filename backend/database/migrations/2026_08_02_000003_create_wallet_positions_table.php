<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_positions', function (Blueprint $table): void {
            $table->id();
            $table->string('wallet')->unique();
            $table->string('staked_balance_raw')->default('0');
            $table->string('staked_balance_formatted')->default('0');
            $table->string('token_balance_raw')->default('0');
            $table->string('token_balance_formatted')->default('0');
            $table->decimal('pool_share_percentage', 8, 4)->default(0.0);
            $table->unsignedBigInteger('last_updated_block')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_positions');
    }
};
