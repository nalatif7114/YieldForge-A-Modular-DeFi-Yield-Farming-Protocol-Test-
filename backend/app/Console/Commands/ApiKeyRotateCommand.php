<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Services\Security\ApiKeyService;
use Illuminate\Console\Command;

class ApiKeyRotateCommand extends Command
{
    protected $signature = 'security:apikey:rotate {id}';

    protected $description = 'Rotate secret key for an existing API Key ID';

    public function handle(ApiKeyService $apiKeyService): int
    {
        $id = (int) $this->argument('id');

        /** @var ApiKey|null $keyModel */
        $keyModel = ApiKey::find($id);

        if (!$keyModel || !$keyModel->user) {
            $this->error("API Key ID #{$id} not found.");
            return Command::FAILURE;
        }

        $user = $keyModel->user;
        $name = $keyModel->name;
        $scopes = $keyModel->scopes ?? [];
        $ipAllowlist = $keyModel->ip_allowlist;

        $keyModel->delete();

        $result = $apiKeyService->createApiKey($user, $name, $scopes, $ipAllowlist);

        $this->info("API Key #{$id} rotated successfully.");
        $this->line("New API Key ID: #{$result['model']->id}");
        $this->line("New Raw Secret Key: {$result['raw_key']}");

        return Command::SUCCESS;
    }
}
