<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Services\Blockchain\Contracts\EventServiceInterface;
use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\DTO\EventDTO;
use App\Services\Blockchain\Exceptions\BlockchainException;
use App\Services\Blockchain\Support\EthereumCodec;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class EventService implements EventServiceInterface
{
    public function __construct(
        private readonly RpcClientInterface $rpcClient,
        private readonly EthereumCodec $codec,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config
    ) {}

    /**
     * Retrieve and decode contract log events.
     *
     * @param string|null $contractKey
     * @param string|null $eventName
     * @param int|null $fromBlock
     * @param int|null $toBlock
     * @param int $limit
     * @return array<int, EventDTO>
     * @throws BlockchainException
     */
    public function getEvents(
        ?string $contractKey = null,
        ?string $eventName = null,
        ?int $fromBlock = null,
        ?int $toBlock = null,
        int $limit = 50
    ): array {
        $cacheKey = sprintf(
            'blockchain:events:%s:%s:%s:%s:%d',
            $contractKey ?? 'all',
            $eventName ?? 'all',
            $fromBlock ?? 'latest',
            $toBlock ?? 'latest',
            $limit
        );

        $ttl = (int) $this->config->get('blockchain.cache_ttl.events', 15);

        return $this->cache->remember($cacheKey, $ttl, function () use ($contractKey, $eventName, $fromBlock, $toBlock, $limit): array {
            try {
                $params = [];

                if ($contractKey !== null) {
                    $address = (string) $this->config->get("blockchain.contracts.{$contractKey}.address");
                    if ($address !== '') {
                        $params['address'] = $address;
                    }
                }

                $params['fromBlock'] = $fromBlock !== null ? '0x' . dechex($fromBlock) : '0x0';
                $params['toBlock'] = $toBlock !== null ? '0x' . dechex($toBlock) : 'latest';

                if ($eventName !== null) {
                    $topic0 = $this->codec->getEventTopic($eventName);
                    if ($topic0 !== null) {
                        $params['topics'] = [$topic0];
                    }
                }

                $rawLogs = $this->rpcClient->call('eth_getLogs', [$params]);

                if (!is_array($rawLogs)) {
                    return [];
                }

                $events = [];
                $count = 0;

                foreach (array_reverse($rawLogs) as $log) {
                    if ($count >= $limit) {
                        break;
                    }

                    if (!is_array($log)) {
                        continue;
                    }

                    $topics = $log['topics'] ?? [];
                    $topic0 = $topics[0] ?? '';
                    $resolvedName = $this->codec->resolveEventName((string) $topic0);

                    $blockNum = isset($log['blockNumber']) ? (int) hexdec(str_replace('0x', '', (string) $log['blockNumber'])) : 0;
                    $logIndex = isset($log['logIndex']) ? (int) hexdec(str_replace('0x', '', (string) $log['logIndex'])) : 0;
                    $txHash = (string) ($log['transactionHash'] ?? '0x0');
                    $address = (string) ($log['address'] ?? '');

                    $parsedParams = $this->decodeEventData($resolvedName, $topics, (string) ($log['data'] ?? '0x'));

                    $events[] = new EventDTO(
                        eventName: $resolvedName,
                        contractAddress: $address,
                        transactionHash: $txHash,
                        blockNumber: $blockNum,
                        logIndex: $logIndex,
                        parameters: $parsedParams
                    );

                    $count++;
                }

                return $events;
            } catch (Throwable $e) {
                throw new BlockchainException("Failed to query log events: {$e->getMessage()}", 500, $e);
            }
        });
    }

    /**
     * Decode topics & data array into parameter key-value pairs.
     *
     * @param string $eventName
     * @param array<int, string> $topics
     * @param string $data
     * @return array<string, mixed>
     */
    private function decodeEventData(string $eventName, array $topics, string $data): array
    {
        $params = [];

        switch ($eventName) {
            case 'Staked':
            case 'Withdrawn':
                if (isset($topics[1])) {
                    $params['user'] = $this->codec->decodeAddress($topics[1]);
                }
                if ($data !== '' && $data !== '0x') {
                    $rawAmt = $this->codec->decodeUint256($data);
                    $params['amount_raw'] = $rawAmt;
                    $params['amount_formatted'] = $this->codec->formatUnits($rawAmt, 18);
                }
                break;

            case 'Transfer':
                if (isset($topics[1])) {
                    $params['from'] = $this->codec->decodeAddress($topics[1]);
                }
                if (isset($topics[2])) {
                    $params['to'] = $this->codec->decodeAddress($topics[2]);
                }
                if ($data !== '' && $data !== '0x') {
                    $rawAmt = $this->codec->decodeUint256($data);
                    $params['value_raw'] = $rawAmt;
                    $params['value_formatted'] = $this->codec->formatUnits($rawAmt, 18);
                }
                break;

            case 'TokensMinted':
                if (isset($topics[1])) {
                    $params['to'] = $this->codec->decodeAddress($topics[1]);
                }
                if ($data !== '' && $data !== '0x') {
                    $rawAmt = $this->codec->decodeUint256($data);
                    $params['amount_raw'] = $rawAmt;
                    $params['amount_formatted'] = $this->codec->formatUnits($rawAmt, 18);
                }
                break;

            case 'TokensBurned':
                if (isset($topics[1])) {
                    $params['from'] = $this->codec->decodeAddress($topics[1]);
                }
                if ($data !== '' && $data !== '0x') {
                    $rawAmt = $this->codec->decodeUint256($data);
                    $params['amount_raw'] = $rawAmt;
                    $params['amount_formatted'] = $this->codec->formatUnits($rawAmt, 18);
                }
                break;

            default:
                $params['raw_topics'] = $topics;
                $params['raw_data'] = $data;
                break;
        }

        return $params;
    }
}
