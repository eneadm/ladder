<?php

namespace Tests;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladder\Ladder;
use Ladder\Role;
use Tests\TestEnums\Roles;

class HasRolesTest extends OrchestraTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Ladder::$permissions = [];
        Ladder::$roles = [];
    }

    public function test_user_returns_the_matching_role()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $role = $user->findRole($user->roles->first());

        $this->assertInstanceOf(Role::class, $role);
        $this->assertSame('admin', $role->key);
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role_when_string_is_given()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertSame(['read', 'create'], $user->rolePermissions('admin'));
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role_when_enum_is_given()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertSame(['read', 'create'], $user->rolePermissions(Roles::ADMIN));
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role_when_array_is_given()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        Ladder::role('user', 'User', [
            'read:post',
            'create:post',
        ])->description('Some admin description');


        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->has(UserRole::factory(['role' => 'user']), 'roles')
            ->create();

        $this->assertSame(['read', 'create', 'read:post', 'create:post'], $user->rolePermissions(['admin', 'user']));
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role_when_collection_is_given()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        Ladder::role('user', 'User', [
            'read:post',
            'create:post',
        ])->description('Some admin description');


        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->has(UserRole::factory(['role' => 'user']), 'roles')
            ->create();

        $this->assertSame(['read', 'create', 'read:post', 'create:post'], $user->rolePermissions(
            collect(['admin', 'user'])
        ));
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role_when_eloquent_collection_is_given()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        Ladder::role('user', 'User', [
            'read:post',
            'create:post',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->has(UserRole::factory(['role' => 'user']), 'roles')
            ->create();

        $this->assertSame(['read', 'create', 'read:post', 'create:post'], $user->rolePermissions($user->roles));
    }

    public function test_rolePermissions_returns_empty_permissions_for_members_without_a_defined_role()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $user = User::factory()->create();

        $this->assertSame([], $user->rolePermissions('admin'));
    }

    public function test_hasPermission_returns_true_for_the_given_user_role_permission_that_he_has()
    {
        Ladder::role('admin', 'Admin', [
            'read',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('read'));
    }

    public function test_hasPermission_returns_false_for_the_given_user_role_permission_that_he_has_not()
    {
        Ladder::role('admin', 'Admin', [])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertFalse($user->hasPermission('read'));
    }

    public function test_hasPermission_returns_true_when_checked_against_any_permission()
    {
        Ladder::role('admin', 'Admin', [
            '*',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('create'));
    }

    public function test_hasRolePermission_returns_true_when_checked_against_any_prefix_permission()
    {
        Ladder::role('admin', 'Admin', [
            '*:create',
            '*:update',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission([
            'post:create',
            'post:update',
        ]));
    }

    public function test_hasPermission_returns_true_when_checked_against_prefixed_permission()
    {
        Ladder::role('admin', 'Admin', [
            'post:create',
            'post:update',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('post:create'));
    }

    public function test_permissions_returns_array_of_assigned_permissions()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
            'update',
            'delete',
        ])->description('Some admin description');

        Ladder::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Some editor description');

        $user = User::factory()
            ->has(UserRole::factory(['role' => 'admin']), 'roles')
            ->has(UserRole::factory(['role' => 'editor']), 'roles')
            ->has(UserRole::factory(['role' => 'unknown']), 'roles') // should be ignored
            ->create();

        $this->assertEquals(
            ['read', 'create', 'update', 'delete'],
            $user->permissions()->toArray(),
        );
    }
}