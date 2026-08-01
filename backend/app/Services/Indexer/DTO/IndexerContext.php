<?php

declare(strict_types=1);

namespace App\Services\Indexer\DTO;

readonly class IndexerContext
{
    public function __construct(
        public int $chainId,
        public string $network,
        public string $rpcEndpoint
    ) {}

    public function toArray(): array
    {
        return [
            'chain_id' => $this->chainId,
            'network' => $this->network,
            'rpc_endpoint' => $this->rpcEndpoint,
        ];
    }
}
