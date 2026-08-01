<?php

declare(strict_types=1);

namespace App\Services\Projection;

use App\Models\PoolSnapshot;
use App\Services\Blockchain\Support\EthereumCodec;
use App\Services\Indexer\Contracts\ProjectionInterface;
use App\Services\Indexer\DomainEvents\AbstractDomainEvent;
use App\Services\Indexer\DomainEvents\PausedDomainEvent;
use App\Services\Indexer\DomainEvents\StakedDomainEvent;
use App\Services\Indexer\DomainEvents\UnpausedDomainEvent;
use App\Services\Indexer\DomainEvents\WithdrawnDomainEvent;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class PoolProjection implements ProjectionInterface
{
    public function __construct(
        private readonly EthereumCodec $codec,
        private readonly ConfigRepository $config
    ) {}

    public function getProjectionName(): string
    {
        return 'PoolProjection';
    }

    public function supports(AbstractDomainEvent $event): bool
    {
        return $event instanceof StakedDomainEvent
            || $event instanceof WithdrawnDomainEvent
            || $event instanceof PausedDomainEvent
            || $event instanceof UnpausedDomainEvent;
    }

    public function handle(AbstractDomainEvent $event): void
    {
        $stakingAddress = (string) $this->config->get('blockchain.contracts.staking.address');
        $tokenAddress = (string) $this->config->get('blockchain.contracts.token.address');

        /** @var PoolSnapshot $pool */
        $pool = PoolSnapshot::firstOrCreate(
            ['pool_id' => 'pool-1'],
            [
                'contract_address' => $stakingAddress,
                'staking_token_address' => $tokenAddress,
                'staking_token_name' => 'YieldForge Token',
                'staking_token_symbol' => 'YFT',
                'staking_token_decimals' => 18,
                'total_staked_raw' => '0',
                'total_staked_formatted' => '0',
                'is_paused' => false,
                'block_number' => $event->blockNumber,
            ]
        );

        if ($event instanceof StakedDomainEvent) {
            $newRaw = (string) bcadd($pool->total_staked_raw, $event->amountRaw);
            $pool->update([
                'total_staked_raw' => $newRaw,
                'total_staked_formatted' => $this->codec->formatUnits($newRaw, 18),
                'block_number' => max($pool->block_number, $event->blockNumber),
            ]);
        } elseif ($event instanceof WithdrawnDomainEvent) {
            $newRaw = (string) bcsub($pool->total_staked_raw, $event->amountRaw);
            if (bccomp($newRaw, '0') < 0) {
                $newRaw = '0';
            }
            $pool->update([
                'total_staked_raw' => $newRaw,
                'total_staked_formatted' => $this->codec->formatUnits($newRaw, 18),
                'block_number' => max($pool->block_number, $event->blockNumber),
            ]);
        } elseif ($event instanceof PausedDomainEvent) {
            $pool->update([
                'is_paused' => true,
                'block_number' => max($pool->block_number, $event->blockNumber),
            ]);
        } elseif ($event instanceof UnpausedDomainEvent) {
            $pool->update([
                'is_paused' => false,
                'block_number' => max($pool->block_number, $event->blockNumber),
            ]);
        }
    }

    public function reset(): void
    {
        PoolSnapshot::query()->truncate();
    }
}
