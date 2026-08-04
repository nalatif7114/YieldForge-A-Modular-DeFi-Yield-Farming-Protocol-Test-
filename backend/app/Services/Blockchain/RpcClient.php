<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\Exceptions\RpcException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Http;
use Throwable;

class RpcClient implements RpcClientInterface
{
    private string $rpcUrl;
    private int $timeout;
    private int $retries;
    private int $retryDelayMs;

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly LogManager $log
    ) {
        $this->rpcUrl = (string) $this->config->get('blockchain.rpc_url');
        $this->timeout = (int) $this->config->get('blockchain.timeout', 10);
        $this->retries = (int) $this->config->get('blockchain.retries', 3);
        $this->retryDelayMs = (int) $this->config->get('blockchain.retry_delay_ms', 100);
    }

    /**
     * Send a single JSON-RPC request to the node.
     *
     * @param string $method
     * @param array<mixed> $params
     * @return mixed
     * @throws RpcException
     */
    public function call(string $method, array $params = []): mixed
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => random_int(1, 999999),
        ];

        $response = $this->sendWithRetry([$payload]);

        $responseData = $response[0] ?? null;

        if (!is_array($responseData)) {
            $this->log->channel('blockchain')->error('RPC response payload is not an array', [
                'method' => $method,
                'response' => $response,
            ]);

            throw new RpcException("Invalid RPC response structure for method [{$method}]");
        }

        if (isset($responseData['error'])) {
            $errorMsg = $responseData['error']['message'] ?? 'Unknown RPC error';
            $errorCode = $responseData['error']['code'] ?? 500;

            $this->log->channel('blockchain')->error('RPC server returned error', [
                'method' => $method,
                'error' => $responseData['error'],
            ]);

            throw new RpcException("RPC Error: {$errorMsg} (Code: {$errorCode})");
        }

        if (config('app.debug')) {
            $this->log->channel('blockchain')->debug('RPC Call Successful', [
                'method' => $method,
                'params' => $params,
            ]);
        }

        return $responseData['result'] ?? null;
    }

    /**
     * Send a batch of JSON-RPC requests.
     *
     * @param array<int, array{method: string, params?: array<mixed>}> $requests
     * @return array<int, mixed>
     * @throws RpcException
     */
    public function batchCall(array $requests): array
    {
        $payloads = [];

        foreach ($requests as $index => $req) {
            $payloads[] = [
                'jsonrpc' => '2.0',
                'method' => $req['method'],
                'params' => $req['params'] ?? [],
                'id' => $index + 1,
            ];
        }

        $responses = $this->sendWithRetry($payloads);

        $results = [];

        foreach ($responses as $resp) {
            if (is_array($resp) && isset($resp['id'])) {
                $id = $resp['id'] - 1;
                if (isset($resp['error'])) {
                    throw new RpcException("RPC Batch Error: " . ($resp['error']['message'] ?? 'Unknown error'));
                }
                $results[$id] = $resp['result'] ?? null;
            }
        }

        return $results;
    }

    /**
     * Execute HTTP POST with retry policy and logging.
     *
     * @param array<int, array<string, mixed>> $payloads
     * @return array<mixed>
     * @throws RpcException
     */
    private function sendWithRetry(array $payloads): array
    {
        $attempt = 0;
        $maxAttempts = $this->retries;
        $body = count($payloads) === 1 ? $payloads[0] : $payloads;

        while ($attempt < $maxAttempts) {
            $attempt++;

            try {
                $http = Http::timeout($this->timeout)->acceptJson();
                if (!$this->config->get('blockchain.verify_ssl', false)) {
                    $http = $http->withoutVerifying();
                }

                $response = $http->post($this->rpcUrl, $body);

                if ($response->successful()) {
                    $json = $response->json();
                    if ($json === null) {
                        throw new RpcException('Malformed JSON received from RPC endpoint');
                    }

                    // If single call wrapped as dict, return in array list
                    return count($payloads) === 1 && isset($json['jsonrpc']) ? [$json] : (array) $json;
                }

                $statusCode = $response->status();

                // Log retry attempt for transient errors
                if (in_array($statusCode, [429, 502, 503, 504], true)) {
                    $this->log->channel('blockchain')->warning("RPC request failed with status {$statusCode}, retrying attempt {$attempt}/{$maxAttempts}", [
                        'status' => $statusCode,
                        'rpc_url' => $this->rpcUrl,
                    ]);

                    usleep(($this->retryDelayMs * (2 ** ($attempt - 1))) * 1000);
                    continue;
                }

                $this->log->channel('blockchain')->error("RPC HTTP error {$statusCode}", [
                    'status' => $statusCode,
                    'body' => $response->body(),
                ]);

                throw new RpcException("RPC server responded with HTTP status {$statusCode}");
            } catch (Throwable $e) {
                if ($e instanceof RpcException) {
                    throw $e;
                }

                $this->log->channel('blockchain')->warning("RPC Connection exception on attempt {$attempt}/{$maxAttempts}: {$e->getMessage()}", [
                    'exception' => $e->getMessage(),
                ]);

                if ($attempt >= $maxAttempts) {
                    $this->log->channel('blockchain')->error("RPC Timeout or unreachable endpoint after {$maxAttempts} attempts: {$e->getMessage()}");
                    throw new RpcException("Unable to connect to Ethereum node: {$e->getMessage()}", 503, $e);
                }

                usleep(($this->retryDelayMs * (2 ** ($attempt - 1))) * 1000);
            }
        }

        throw new RpcException('RPC execution failed after maximum retries');
    }
}
