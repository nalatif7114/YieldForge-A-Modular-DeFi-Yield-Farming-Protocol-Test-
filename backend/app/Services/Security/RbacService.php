<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\ApiKey;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class RbacService
{
    /**
     * Check if actor (User or ApiKey) possesses a permission.
     *
     * @param User|ApiKey $actor
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission(User|ApiKey $actor, string $permissionSlug): bool
    {
        if ($actor instanceof ApiKey) {
            $scopes = $actor->scopes ?? [];
            return in_array('*', $scopes, true) || in_array($permissionSlug, $scopes, true);
        }

        // Admin override
        if ($actor->hasRole('admin')) {
            return true;
        }

        return $actor->hasPermission($permissionSlug);
    }

    public function assignRole(User $user, string $roleSlug): void
    {
        /** @var Role|null $role */
        $role = Role::where('slug', $roleSlug)->first();
        if ($role && !$user->hasRole($roleSlug)) {
            $user->roles()->attach($role->id);
        }
    }

    public function syncPermissions(Role $role, array $permissionSlugs): void
    {
        $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id')->all();
        $role->permissions()->sync($permissionIds);
    }
}
