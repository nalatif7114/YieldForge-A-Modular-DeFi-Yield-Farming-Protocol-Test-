<?php

declare(strict_types=1);

namespace Tests\Unit\Research;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_research_build_command_executes_successfully(): void
    {
        $this->artisan('research:build')
            ->expectsOutputToContain('Building YieldForge Research Datasets')
            ->assertExitCode(0);

        $this->assertDatabaseHas('research_datasets', ['type' => 'wallet_behavior']);
    }

    public function test_research_validate_command_executes_successfully(): void
    {
        $this->artisan('research:validate')
            ->expectsOutputToContain('Validating YieldForge Research Datasets Data Quality')
            ->assertExitCode(0);
    }

    public function test_research_export_command_executes_successfully(): void
    {
        $this->artisan('research:export', ['type' => 'wallet_behavior', '--format' => 'json'])
            ->expectsOutputToContain('Exporting dataset [wallet_behavior]')
            ->assertExitCode(0);

        $this->assertDatabaseHas('research_exports', ['dataset_name' => 'wallet_behavior']);
    }
}
