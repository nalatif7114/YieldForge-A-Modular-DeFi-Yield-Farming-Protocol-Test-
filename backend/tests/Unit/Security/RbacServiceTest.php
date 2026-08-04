<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\ApiKey;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\Security\RbacService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacServiceTest extends TestCase
{
    use RefreshDatabase;

    private RbacService $rbacService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rbacService = $this->app->make(RbacService::class);
    }

    public function test_user_permission_and_admin_override(): void
    {
        /** @var User $user */
        $user = User::create(['name' => 'Operator User', 'email' => 'op@yieldforge.io', 'password' => bcrypt('secret')]);
        /** @var Role $role */
        $role = Role::create(['name' => 'Operator', 'slug' => 'operator']);
        /** @var Permission $perm */
        $perm = Permission::create(['name' => 'Sync Indexer', 'slug' => 'indexer.sync']);

        $role->permissions()->attach($perm->id);
        $user->roles()->attach($role->id);

        $this->assertTrue($this->rbacService->hasPermission($user, 'indexer.sync'));
        $this->assertFalse($this->rbacService->hasPermission($user, 'users.manage'));

        /** @var User $admin */
        $admin = User::create(['name' => 'Admin User', 'email' => 'admin@yieldforge.io', 'password' => bcrypt('secret')]);
        /** @var Role $adminRole */
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $admin->roles()->attach($adminRole->id);

        $this->assertTrue($this->rbacService->hasPermission($admin, 'users.manage'));
    }

    public function test_api_key_scopes_permission_evaluation(): void
    {
        /** @var ApiKey $key */
        $key = ApiKey::create([
            'name' => 'Test API Key',
            'key_prefix' => 'yf_live_1234',
            'key_hash' => hash('sha256', 'yf_live_secret'),
            'scopes' => ['monitoring.view'],
        ]);

        $this->assertTrue($this->rbacService->hasPermission($key, 'monitoring.view'));
        $this->assertFalse($this->rbacService->hasPermission($key, 'indexer.sync'));
    }
}
