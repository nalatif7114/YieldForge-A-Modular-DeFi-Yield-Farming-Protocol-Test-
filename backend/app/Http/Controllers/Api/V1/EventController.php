<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Services\Blockchain\Contracts\EventServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function __construct(
        private readonly EventServiceInterface $eventService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $contractKey = $request->query('contract');
        $eventName = $request->query('event');
        $fromBlock = $request->query('from_block') ? (int) $request->query('from_block') : null;
        $toBlock = $request->query('to_block') ? (int) $request->query('to_block') : null;
        $limit = $request->query('limit') ? (int) $request->query('limit') : 50;

        $events = $this->eventService->getEvents(
            contractKey: is_string($contractKey) ? $contractKey : null,
            eventName: is_string($eventName) ? $eventName : null,
            fromBlock: $fromBlock,
            toBlock: $toBlock,
            limit: $limit
        );

        return EventResource::collection($events);
    }
}
