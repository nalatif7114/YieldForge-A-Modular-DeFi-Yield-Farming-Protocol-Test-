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
        // Update users table for wallet_address & last_login_at
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'wallet_address')) {
                    $table->string('wallet_address')->nullable()->unique()->index()->after('email');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('password');
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('is_active');
                }
            });
        }

        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // role_user pivot table
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        // permission_role pivot table
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
        });

        // api_keys table
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('key_prefix')->index();
            $table->string('key_hash')->unique()->index();
            $table->json('scopes')->nullable();
            $table->json('ip_allowlist')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // refresh_tokens table
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token_hash')->unique()->index();
            $table->string('device_info')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('expires_at')->index();
            $table->boolean('revoked')->default(false)->index();
            $table->timestamps();
        });

        // wallet_nonces table
        Schema::create('wallet_nonces', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_address')->index();
            $table->string('nonce')->unique()->index();
            $table->timestamp('expires_at')->index();
            $table->boolean('used')->default(false)->index();
            $table->timestamps();
        });

        // audit_logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->index();
            $table->string('resource')->index();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        // security_events table
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info')->index();
            $table->string('ip_address')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });

        // login_attempts table
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('identity')->index();
            $table->string('ip_address')->nullable();
            $table->boolean('successful')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('wallet_nonces');
        Schema::dropIfExists('refresh_tokens');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
