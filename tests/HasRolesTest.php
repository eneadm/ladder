<?php

namespace Tests;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladder\Ladder;
use Ladder\Models\ModelRole;
use Ladder\Role;

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
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $role = $user->findRole($user->roles->first());

        $this->assertInstanceOf(Role::class, $role);
        $this->assertSame('admin', $role->key);
    }

    public function test_rolePermissions_returns_permissions_for_the_users_role()
    {
        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertSame(['read', 'create'], $user->rolePermissions('admin'));
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
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('read'));
    }

    public function test_hasPermission_returns_false_for_the_given_user_role_permission_that_he_has_not()
    {
        Ladder::role('admin', 'Admin', [])->description('Some admin description');

        $user = User::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertFalse($user->hasPermission('read'));
    }

    public function test_hasPermission_returns_true_when_checked_against_any_permission()
    {
        Ladder::role('admin', 'Admin', [
            '*',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('create'));
    }

    public function test_hasPermission_returns_true_when_checked_against_prefixed_permission()
    {
        Ladder::role('admin', 'Admin', [
            'post:create',
            'post:update',
        ])->description('Some admin description');

        $user = User::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($user->hasPermission('post:create'));
    }

    public function test_has_roles_can_support_multiple_models()
    {
        Ladder::role('admin', 'Admin', [
            'post:create',
            'post:update',
        ])->description('Some admin description');

        $folder = Folder::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertTrue($folder->hasPermission('post:create'));
    }

    public function test_role_pivot_is_deleted_when_model_is()
    {
        $folder = Folder::factory()
            ->has(ModelRole::factory(['role' => 'admin']), 'roles')
            ->create();

        $this->assertCount(1, ModelRole::all());

        $folder->delete();

        $this->assertCount(0, ModelRole::all());
    }
}
