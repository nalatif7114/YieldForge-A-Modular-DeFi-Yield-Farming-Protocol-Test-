<?php

declare(strict_types=1);

namespace App\Services\Projection;

use App\Models\BlockchainEvent;
use App\Models\PoolSnapshot;
use App\Models\ProtocolStatistic;
use App\Models\WalletPosition;
use App\Services\Blockchain\Support\EthereumCodec;
use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use App\Services\Indexer\DomainEvents\StakedDomainEvent;
use App\Services\Indexer\DomainEvents\TokensBurnedDomainEvent;
use App\Services\Indexer\DomainEvents\TokensMintedDomainEvent;
use App\Services\Indexer\DomainEvents\WithdrawnDomainEvent;

class ProtocolProjection implements ProjectionInterface
{
    public function __construct(
        private readonly EthereumCodec $codec
    ) {}

    public function getProjectionName(): string
    {
        return 'ProtocolProjection';
    }

    public function supports(AbstractDomainEvent $event): bool
    {
        return true; // Recalculates aggregate protocol statistics
    }

    public function handle(AbstractDomainEvent $event): void
    {
        $this->refresh($event->blockNumber);
    }

    public function refresh(int $latestBlock = 0): void
    {
        /** @var PoolSnapshot|null $pool */
        $pool = PoolSnapshot::where('pool_id', 'pool-1')->first();
        $tvlRaw = $pool ? $pool->total_staked_raw : '0';
        $tvlFormatted = $this->codec->formatUnits($tvlRaw, 18);

        $stakersCount = WalletPosition::where('staked_balance_raw', '>', '0')->count();
        $totalEventsCount = BlockchainEvent::count();

        /** @var ProtocolStatistic $stat */
        $stat = ProtocolStatistic::firstOrCreate(
            ['id' => 1],
            [
                'total_value_locked_raw' => '0',
                'total_value_locked_formatted' => '0',
                'total_stakers_count' => 0,
                'total_events_processed' => 0,
                'total_tokens_minted_raw' => '0',
                'total_tokens_burned_raw' => '0',
                'latest_indexed_block' => 0,
            ]
        );

        $stat->update([
            'total_value_locked_raw' => $tvlRaw,
            'total_value_locked_formatted' => $tvlFormatted,
            'total_stakers_count' => $stakersCount,
            'total_events_processed' => $totalEventsCount,
            'latest_indexed_block' => max($stat->latest_indexed_block, $latestBlock),
        ]);
    }

    public function reset(): void
    {
        ProtocolStatistic::query()->truncate();
    }
}
