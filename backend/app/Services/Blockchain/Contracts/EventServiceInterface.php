<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Contracts;

use App\Services\Blockchain\DTO\EventDTO;

interface EventServiceInterface
{
    /**
     * Retrieve and decode contract log events.
     *
     * @param string|null $contractKey Contract key from config (token or staking) or null for all
     * @param string|null $eventName Specific event name (Staked, Withdrawn, Transfer, etc.)
     * @param int|null $fromBlock
     * @param int|null $toBlock
     * @param int $limit
     * @return array<int, EventDTO>
     */
    public function getEvents(
        ?string $contractKey = null,
        ?string $eventName = null,
        ?int $fromBlock = null,
        ?int $toBlock = null,
        int $limit = 50
    ): array;
}
