<?php

namespace Ladder;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ladder\Models\UserRole;

trait HasRoles
{
    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    public function findRole(UserRole $userRole): ?Role
    {
        return Ladder::findRole($userRole->role);
    }

    public function rolePermissions(string $role): ?array
    {
        $userRole = $this->roles->where('role', $role)->first();

        if (!$userRole) {
            return [];
        }

        return $this->findRole($userRole)?->permissions ?: [];
    }

    public function hasRolePermission(string $role, string $permission): bool
    {
        $permissions = $this->rolePermissions($role);

        return in_array($permission, $permissions) ||
            in_array('*', $permissions) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles
            ->map(fn ($userRole) => $this->hasRolePermission($userRole->role, $permission))
            ->containsStrict(true);
    }

    public function permissions(): Collection
    {
        return $this->roles
            ->map(fn ($role) => $this->rolePermissions($role->attributes['role']))
            ->flatten()
            ->unique();
    }

}
