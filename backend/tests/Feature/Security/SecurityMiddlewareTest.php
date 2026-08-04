<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Http\Middleware\AdaptiveRateLimiterMiddleware;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\RbacMiddleware;
use App\Http\Middleware\ReplayProtectionMiddleware;
use App\Http\Middleware\RequestSignatureMiddleware;
use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SecurityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_jwt_auth_middleware_blocks_unauthenticated_request(): void
    {
        $middleware = $this->app->make(JwtAuthMiddleware::class);
        $request = Request::create('/api/v1/auth/me', 'GET');

        $response = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_jwt_auth_middleware_allows_valid_token(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Valid User',
            'email' => 'valid@yieldforge.io',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        /** @var JwtService $jwtService */
        $jwtService = $this->app->make(JwtService::class);
        $token = $jwtService->issueAccessToken($user);

        $middleware = $this->app->make(JwtAuthMiddleware::class);
        $request = Request::create('/api/v1/auth/me', 'GET');
        $request->headers->set('Authorization', "Bearer {$token}");

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['status' => 'ok', 'user_id' => $req->attributes->get('auth_user')->id]);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_rbac_middleware_enforces_permissions(): void
    {
        /** @var User $user */
        $user = User::create(['name' => 'Basic User', 'email' => 'basic@yieldforge.io', 'password' => bcrypt('password'), 'is_active' => true]);

        $middleware = $this->app->make(RbacMiddleware::class);
        $request = Request::create('/api/v1/indexer/sync', 'POST');
        $request->attributes->set('auth_user', $user);

        $response = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        }, 'indexer.sync');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_request_signature_middleware_validates_headers(): void
    {
        $middleware = $this->app->make(RequestSignatureMiddleware::class);
        $request = Request::create('/api/v1/secure', 'POST');

        $response = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        });

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_replay_protection_middleware_blocks_replayed_nonce(): void
    {
        $middleware = new ReplayProtectionMiddleware();
        $request = Request::create('/api/v1/secure', 'POST');
        $request->headers->set('X-Nonce', 'unique_nonce_999');

        $res1 = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        });
        $this->assertEquals(200, $res1->getStatusCode());

        $res2 = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        });
        $this->assertEquals(409, $res2->getStatusCode());
    }

    public function test_adaptive_rate_limiter_middleware_adds_headers(): void
    {
        $middleware = new AdaptiveRateLimiterMiddleware();
        $request = Request::create('/api/v1/public', 'GET');

        $response = $middleware->handle($request, function () {
            return response()->json(['status' => 'ok']);
        }, 100, 60);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
    }
}
