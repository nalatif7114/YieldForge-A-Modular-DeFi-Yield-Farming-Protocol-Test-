<?php

declare(strict_types=1);

namespace Tests\Unit\Monitoring;

use App\Models\MonitoringAlert;
use App\Services\Monitoring\AlertEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertEngineServiceTest extends TestCase
{
    use RefreshDatabase;

    private AlertEngineService $alertEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertEngine = $this->app->make(AlertEngineService::class);
    }

    public function test_acknowledge_alert_updates_status(): void
    {
        /** @var MonitoringAlert $alert */
        $alert = MonitoringAlert::create([
            'rule_name' => 'IndexerLagHigh',
            'severity' => 'critical',
            'component' => 'indexer',
            'message' => 'Lag > 50 blocks',
            'status' => 'active',
        ]);

        $acknowledged = $this->alertEngine->acknowledgeAlert($alert->id);

        $this->assertNotNull($acknowledged);
        $this->assertEquals('acknowledged', $acknowledged->status);
        $this->assertDatabaseHas('monitoring_alerts', [
            'id' => $alert->id,
            'status' => 'acknowledged',
        ]);
    }

    public function test_get_rules_returns_configured_alert_rules(): void
    {
        $rules = $this->alertEngine->getRules();

        $this->assertIsArray($rules);
        $this->assertGreaterThanOrEqual(4, count($rules));
        $this->assertEquals('IndexerLagHigh', $rules[0]['rule_name']);
    }
}
