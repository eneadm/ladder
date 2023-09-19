<?php

namespace Ladder;

use BackedEnum;
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

    public function findRole(UserRole $userRole): ?Role
    {
        return Ladder::findRole($userRole->role);
    }

    public function hasRole(string|array|Collection|BackedEnum $roles): bool
    {
        return $this->filterRoles($roles)->isNotEmpty();
    }

    public function rolePermissions(string|array|Collection|UserRole|BackedEnum $roles): array
    {
        return $this->filterRoles($roles)
            ->map(fn ($role) => (array) optional($this->findRole($role))->permissions)
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function filterRoles(string|array|Collection|UserRole|BackedEnum $roles): Collection
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if ($roles instanceof BackedEnum) {
            $roles = [$roles->value];
        }

        if ($roles instanceof Collection) {
            $pivot = $roles->filter(fn ($role) => $role instanceof UserRole);

            if ($pivot->isNotEmpty()) {
                $roles = $pivot->map(fn ($role) => $role->role);
            }
        }

        return $this->roles->whereIn('role', collect($roles)->filter());
    }

    public function hasRolePermission(string|array|Collection|BackedEnum $roles,
                                      string|array|Collection|BackedEnum $permissions): bool
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        if ($permissions instanceof BackedEnum) {
            $permissions = [$permissions->value];
        }

        $rolePermissions = collect($this->rolePermissions($roles));

        $permissions = collect($permissions);

        return $permissions->contains(fn ($permission) =>
            $rolePermissions->contains($permission) ||
            $rolePermissions->contains('*') ||
            (Str::endsWith($permission, ':create') && $rolePermissions->contains('*:create')) ||
            (Str::endsWith($permission, ':update') && $rolePermissions->contains('*:update'))
        );
    }

    public function hasPermission(string|array|Collection|BackedEnum $permissions): bool
    {
        return $this->hasRolePermission($this->roles, $permissions);
    }

    public function permissions(): Collection
    {
        return collect($this->rolePermissions($this->roles));
    }
}
