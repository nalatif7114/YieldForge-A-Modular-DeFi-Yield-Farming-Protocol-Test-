<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Monitoring Telemetry', 'slug' => 'monitoring.view', 'description' => 'Access system health and operational dashboards'],
            ['name' => 'View Analytics Data', 'slug' => 'analytics.view', 'description' => 'Access TVL, APY, and protocol historical analytics'],
            ['name' => 'Export Analytics & Events', 'slug' => 'analytics.export', 'description' => 'Download event store and metrics exports'],
            ['name' => 'Manage Operational Alerts', 'slug' => 'alerts.manage', 'description' => 'Acknowledge and configure monitoring alert rules'],
            ['name' => 'Sync Blockchain Indexer', 'slug' => 'indexer.sync', 'description' => 'Trigger real-time block sync'],
            ['name' => 'Replay Event Sourcing Engine', 'slug' => 'indexer.replay', 'description' => 'Trigger event store projections replay'],
            ['name' => 'Manage Users & Security', 'slug' => 'users.manage', 'description' => 'Manage platform users, roles, and API keys'],
            ['name' => 'View Security Telemetry', 'slug' => 'security.manage', 'description' => 'Access security dashboard and audit logs'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        $roles = [
            ['name' => 'System Administrator', 'slug' => 'admin', 'description' => 'Full administrative access to all features'],
            ['name' => 'Protocol Operator', 'slug' => 'operator', 'description' => 'Operational management and indexer control'],
            ['name' => 'Data Analyst', 'slug' => 'analyst', 'description' => 'Read access to analytics and exports'],
            ['name' => 'Read Only', 'slug' => 'read_only', 'description' => 'Basic read access to dashboards'],
            ['name' => 'API Client', 'slug' => 'api_client', 'description' => 'Automated integration access'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }

        /** @var Role $adminRole */
        $adminRole = Role::where('slug', 'admin')->first();
        /** @var Role $operatorRole */
        $operatorRole = Role::where('slug', 'operator')->first();
        /** @var Role $analystRole */
        $analystRole = Role::where('slug', 'analyst')->first();
        /** @var Role $readOnlyRole */
        $readOnlyRole = Role::where('slug', 'read_only')->first();

        $allPermIds = Permission::pluck('id')->all();
        $adminRole->permissions()->sync($allPermIds);

        $operatorPerms = Permission::whereIn('slug', ['monitoring.view', 'analytics.view', 'alerts.manage', 'indexer.sync', 'indexer.replay'])->pluck('id')->all();
        $operatorRole->permissions()->sync($operatorPerms);

        $analystPerms = Permission::whereIn('slug', ['monitoring.view', 'analytics.view', 'analytics.export'])->pluck('id')->all();
        $analystRole->permissions()->sync($analystPerms);

        $readOnlyPerms = Permission::whereIn('slug', ['monitoring.view', 'analytics.view'])->pluck('id')->all();
        $readOnlyRole->permissions()->sync($readOnlyPerms);

        /** @var User $adminUser */
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@yieldforge.io'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('AdminSecretPassword123!'),
                'wallet_address' => '0x86b6346984f6f9380a94bc0d2c006044649f2077',
                'is_active' => true,
            ]
        );

        if (!$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole->id);
        }
    }
}
