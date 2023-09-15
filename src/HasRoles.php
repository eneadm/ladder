<?php

namespace Ladder;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Ladder\Models\ModelRole;

trait HasRoles
{
    public static function bootHasRoles(): void
    {
        static::deleting(fn ($model) => $model->roles()->delete());
    }

    public function roles(): MorphMany
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    public function findRole(ModelRole $userRole): ?Role
    {
        return Ladder::findRole($userRole->role);
    }

    public function rolePermissions(string $role): ?array
    {
        $userRole = $this->roles->where('role', $role)->first();

        return $userRole
            ? $this->findRole($userRole)->permissions
            : [];
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
}
