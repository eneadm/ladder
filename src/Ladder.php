<?php

namespace Ladder;

class Ladder
{
    public static array $roles = [];

    public static array $permissions = [];

    public static function findRole(string $key): ?Role
    {
        return static::$roles[$key] ?? null;
    }

    public static function role(string $key, string $name, array $permissions)
    {
        static::$permissions = collect(array_merge(static::$permissions, $permissions))
            ->unique()
            ->sort()
            ->values()
            ->all();

        return tap(new Role($key, $name, $permissions), function ($role) use ($key) {
            static::$roles[$key] = $role;
        });
    }

    public static function hasPermissions(): bool
    {
        return count(static::$permissions) > 0;
    }

    public static function permissions(array $permissions): static
    {
        static::$permissions = $permissions;

        return new static;
    }
}
