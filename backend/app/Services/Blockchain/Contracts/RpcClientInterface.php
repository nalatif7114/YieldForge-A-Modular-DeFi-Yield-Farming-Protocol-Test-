<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Contracts;

interface RpcClientInterface
{
    /**
     * Send a single JSON-RPC request to the node.
     *
     * @param string $method
     * @param array<mixed> $params
     * @return mixed
     */
    public function call(string $method, array $params = []): mixed;

    /**
     * Send a batch of JSON-RPC requests.
     *
     * @param array<int, array{method: string, params?: array<mixed>}> $requests
     * @return array<int, mixed>
     */
    public function batchCall(array $requests): array;
}
