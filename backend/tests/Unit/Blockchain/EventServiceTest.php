<?php

declare(strict_types=1);

namespace Tests\Unit\Blockchain;

use App\Services\Blockchain\Contracts\EventServiceInterface;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    private EventServiceInterface $eventService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventService = $this->app->make(EventServiceInterface::class);
    }

    public function test_get_events_decodes_staked_event(): void
    {
        Http::fake([
            '*' => Http::response([
                'jsonrpc' => '2.0',
                'result' => [
                    [
                        'address' => '0xe7f1725E7734CE288F8367e1Bb143E90bb3F0512',
                        'topics' => [
                            '0x9e71bc8eea02a63969f509818f2dafb9254532904319f9dbda79b67bd34a5f3d', // Staked(address,uint256)
                            '0x000000000000000000000000f39fd6e51aad88f6f4ce6ab8827279cfffb92266', // user address
                        ],
                        'data' => '0x0000000000000000000000000000000000000000000000008782d0d00d400000', // amount
                        'blockNumber' => '0x64',
                        'transactionHash' => '0xabc123',
                        'logIndex' => '0x1',
                    ],
                ],
                'id' => 1,
            ]),
        ]);

        $events = $this->eventService->getEvents('staking', 'Staked');

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals('Staked', $events[0]->eventName);
        $this->assertEquals('0xabc123', $events[0]->transactionHash);
        $this->assertEquals('0xf39fd6e51aad88f6f4ce6ab8827279cfffb92266', $events[0]->parameters['user']);
    }
}
