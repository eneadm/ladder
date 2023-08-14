<?php

namespace Ladder;

class Ladder
{
    public static array $roles = [];

    public static array $permissions = [];

    public static string $userModel = 'App\\Models\\User';

    public static string $membershipModel = 'App\\Models\\Membership';

    public static function hasRoles(): int
    {
        return count(static::$roles) > 0;
    }

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

    public static function userModel(): string
    {
        return static::$userModel;
    }

    public static function useUserModel(string $model): static
    {
        static::$userModel = $model;

        return new static;
    }

    public static function membershipModel(): string
    {
        return static::$membershipModel;
    }

    public static function useMembershipModel(string $model): static
    {
        static::$membershipModel = $model;

        return new static;
    }
}