<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BlockchainEvent;
use App\Models\IndexedBlock;
use App\Models\ProjectionCheckpoint;
use Illuminate\Console\Command;

class BlockchainVerifyCommand extends Command
{
    protected $signature = 'blockchain:verify';

    protected $description = 'Verify indexer database integrity and projection checkpoints';

    public function handle(): int
    {
        $indexedBlocks = IndexedBlock::count();
        $eventsCount = BlockchainEvent::count();
        $checkpoints = ProjectionCheckpoint::all();

        $this->info("=== YieldForge Indexer Verification ===");
        $this->line("Indexed Blocks: {$indexedBlocks}");
        $this->line("Total Events in Store: {$eventsCount}");
        $this->line("Projection Checkpoints:");

        foreach ($checkpoints as $cp) {
            $this->line(" - {$cp->projection_name}: block #{$cp->last_processed_block} (v{$cp->projection_version})");
        }

        return Command::SUCCESS;
    }
}
