<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\BlockchainEvent;
use App\Services\Blockchain\DTO\EventDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = BlockchainEvent::query()->where('removed', false);

        if ($request->query('contract') !== null) {
            $key = (string) $request->query('contract');
            $address = config("blockchain.contracts.{$key}.address");
            if ($address) {
                $query->where('contract_address', strtolower((string) $address));
            }
        }

        if ($request->query('event') !== null) {
            $query->where('event_name', (string) $request->query('event'));
        }

        if ($request->query('from_block') !== null) {
            $query->where('block_number', '>=', (int) $request->query('from_block'));
        }

        if ($request->query('to_block') !== null) {
            $query->where('block_number', '<=', (int) $request->query('to_block'));
        }

        $limit = $request->query('limit') ? (int) $request->query('limit') : 50;

        $events = $query->orderBy('block_number', 'desc')
            ->orderBy('log_index', 'desc')
            ->limit($limit)
            ->get()
            ->map(function (BlockchainEvent $event) {
                return new EventDTO(
                    eventName: $event->event_name,
                    contractAddress: $event->contract_address,
                    transactionHash: $event->transaction_hash,
                    blockNumber: (int) $event->block_number,
                    logIndex: (int) $event->log_index,
                    parameters: is_array($event->payload) ? $event->payload : []
                );
            });

        return EventResource::collection($events);
    }
}
