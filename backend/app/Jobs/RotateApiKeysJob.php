<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ApiKey;
use App\Services\Security\ApiKeyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RotateApiKeysJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $apiKeyId
    ) {}

    public function handle(ApiKeyService $apiKeyService): void
    {
        /** @var ApiKey|null $keyModel */
        $keyModel = ApiKey::find($this->apiKeyId);
        if (!$keyModel || !$keyModel->user) {
            return;
        }

        $user = $keyModel->user;
        $name = $keyModel->name;
        $scopes = $keyModel->scopes ?? [];
        $ipAllowlist = $keyModel->ip_allowlist;

        $keyModel->delete();
        $apiKeyService->createApiKey($user, $name, $scopes, $ipAllowlist);
    }
}
