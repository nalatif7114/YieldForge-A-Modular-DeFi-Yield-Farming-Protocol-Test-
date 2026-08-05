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
        // research_datasets table
        Schema::create('research_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->enum('type', [
                'wallet_behavior',
                'pool_activity',
                'reward_distribution',
                'protocol_growth',
                'staking_history',
                'transaction_features',
            ])->index();
            $table->string('version')->default('1.0.0');
            $table->unsignedInteger('row_count')->default(0);
            $table->unsignedInteger('quality_score')->default(100);
            $table->enum('status', ['building', 'ready', 'failed'])->default('ready')->index();
            $table->timestamps();
        });

        // feature_sets table
        Schema::create('feature_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('version')->default('1.0.0')->index();
            $table->unsignedInteger('feature_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // wallet_features table
        Schema::create('wallet_features', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_address')->unique()->index();
            $table->unsignedInteger('wallet_age_days')->default(0);
            $table->string('average_stake_formatted')->default('0');
            $table->float('staking_frequency')->default(0.0);
            $table->unsignedInteger('holding_duration_days')->default(0);
            $table->float('reward_velocity')->default(0.0);
            $table->float('stake_growth_pct')->default(0.0);
            $table->float('unstake_ratio')->default(0.0);
            $table->unsignedInteger('active_days')->default(0);
            $table->float('transaction_interval_hours')->default(0.0);
            $table->unsignedInteger('pool_diversity_count')->default(1);
            $table->string('feature_version')->default('1.0.0')->index();
            $table->timestamps();
        });

        // pool_features table
        Schema::create('pool_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pool_id')->unique()->index();
            $table->string('total_staked_formatted')->default('0');
            $table->unsignedInteger('active_stakers_count')->default(0);
            $table->float('transaction_velocity')->default(0.0);
            $table->float('utilization_rate')->default(0.0);
            $table->string('feature_version')->default('1.0.0')->index();
            $table->timestamps();
        });

        // dataset_versions table
        Schema::create('dataset_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained('research_datasets')->onDelete('cascade');
            $table->string('version')->index();
            $table->string('checksum')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('row_count')->default(0);
            $table->timestamps();
        });

        // research_exports table
        Schema::create('research_exports', function (Blueprint $table) {
            $table->id();
            $table->string('dataset_name')->index();
            $table->enum('format', ['csv', 'json', 'parquet'])->default('json');
            $table->unsignedInteger('row_count')->default(0);
            $table->string('file_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_exports');
        Schema::dropIfExists('dataset_versions');
        Schema::dropIfExists('pool_features');
        Schema::dropIfExists('wallet_features');
        Schema::dropIfExists('feature_sets');
        Schema::dropIfExists('research_datasets');
    }
};
