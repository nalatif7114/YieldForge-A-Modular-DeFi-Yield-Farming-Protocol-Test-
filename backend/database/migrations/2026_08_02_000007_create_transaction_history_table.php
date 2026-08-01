<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_history', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_hash')->index();
            $table->string('wallet')->index();
            $table->string('event_name');
            $table->string('amount_raw')->default('0');
            $table->string('amount_formatted')->default('0');
            $table->unsignedBigInteger('block_number')->default(0);
            $table->timestamp('timestamp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_history');
    }
};
