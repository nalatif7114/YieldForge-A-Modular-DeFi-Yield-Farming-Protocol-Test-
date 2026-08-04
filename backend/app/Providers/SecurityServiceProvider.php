<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Security\ApiKeyService;
use App\Services\Security\AuditLoggerService;
use App\Services\Security\JwtService;
use App\Services\Security\RbacService;
use App\Services\Security\RequestSignatureService;
use App\Services\Security\SecurityDashboardService;
use App\Services\Security\SiweAuthService;
use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JwtService::class);
        $this->app->singleton(SiweAuthService::class);
        $this->app->singleton(RbacService::class);
        $this->app->singleton(ApiKeyService::class);
        $this->app->singleton(AuditLoggerService::class);
        $this->app->singleton(RequestSignatureService::class);
        $this->app->singleton(SecurityDashboardService::class);
    }

    public function boot(): void
    {
        //
    }
}
