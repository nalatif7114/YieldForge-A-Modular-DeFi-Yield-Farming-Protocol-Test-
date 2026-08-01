<?php

declare(strict_types=1);

namespace Tests\Unit\Indexer;

use App\Models\IndexedBlock;
use App\Services\Indexer\Contracts\ReorgDetectorInterface;
use App\Services\Indexer\DTO\IndexerContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReorgDetectorTest extends TestCase
{
    use RefreshDatabase;

    private ReorgDetectorInterface $reorgDetector;
    private IndexerContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reorgDetector = $this->app->make(ReorgDetectorInterface::class);
        $this->context = new IndexerContext(11155111, 'sepolia', 'http://127.0.0.1');
    }

    public function test_detects_reorg_when_parent_hash_mismatches(): void
    {
        IndexedBlock::create([
            'chain_id' => 11155111,
            'network' => 'sepolia',
            'block_number' => 99,
            'block_hash' => '0xoldhash99',
            'status' => 'processed',
        ]);

        $divergence = $this->reorgDetector->detectReorg($this->context, 100, '0xnewdivergentparent');

        $this->assertEquals(98, $divergence);
    }

    public function test_rollback_to_marks_blocks_reverted(): void
    {
        IndexedBlock::create([
            'chain_id' => 11155111,
            'network' => 'sepolia',
            'block_number' => 100,
            'block_hash' => '0xhash100',
            'status' => 'processed',
        ]);

        $this->reorgDetector->rollbackTo($this->context, 99);

        $this->assertDatabaseHas('indexed_blocks', [
            'block_number' => 100,
            'status' => 'reverted',
        ]);
    }
}
