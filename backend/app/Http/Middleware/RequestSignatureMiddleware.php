<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Security\RequestSignatureService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestSignatureMiddleware
{
    public function __construct(
        private readonly RequestSignatureService $signatureService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $nonce = $request->header('X-Nonce');

        if (!$signature || !$timestamp || !$nonce) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing required signature headers (X-Signature, X-Timestamp, X-Nonce).',
            ], 400);
        }

        $secret = (string) config('app.key', 'YieldForgeSecretKey32BytesLongMin');
        $body = (string) $request->getContent();

        $valid = $this->signatureService->verify(
            secret: $secret,
            signature: $signature,
            timestamp: $timestamp,
            nonce: $nonce,
            body: $body
        );

        if (!$valid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, expired, or replayed request signature.',
            ], 401);
        }

        return $next($request);
    }
}
