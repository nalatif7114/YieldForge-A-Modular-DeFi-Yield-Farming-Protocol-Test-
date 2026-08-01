<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('wallet')->index();
            $table->string('token_address');
            $table->string('balance_raw')->default('0');
            $table->string('balance_formatted')->default('0');
            $table->string('pending_rewards_raw')->default('0');
            $table->string('pending_rewards_formatted')->default('0');
            $table->unsignedBigInteger('block_number')->default(0);
            $table->timestamps();

            $table->unique(['wallet', 'token_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_snapshots');
    }
};
