<?php

declare(strict_types=1);

namespace Tests\Feature\Research;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_research_export_json_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/export/wallet_behavior?format=json');
        $res->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_research_export_csv_endpoint(): void
    {
        $res = $this->getJson('/api/v1/research/export/wallet_behavior?format=csv');
        $res->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
