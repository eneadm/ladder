<?php

namespace Tests;

use App\Models\User;
use Ladder\HasRoles;
use Ladder\Ladder;

class ScopedRolesTest extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        Ladder::role('editor', 'Editor', [
            'read',
            'create',
        ])->description('Some admin description');
    }

    public function test_roles_can_be_scoped_by_tenant()
    {
        /** @var HasRoles $user */
        $user = User::factory()->create();

        $user->assignRole('admin', 'tenant1');
        $user->assignRole('editor', 'tenant2');

        $this->assertTrue($user->forTenant('tenant1')->hasRole('admin'));
        $this->assertFalse($user->forTenant('tenant1')->hasRole('editor'));

        $this->assertFalse($user->forTenant('tenant2')->hasRole('admin'));
        $this->assertTrue($user->forTenant('tenant2')->hasRole('editor'));
    }

    public function test_roles_can_be_globally_scoped_by_tenant()
    {
        Ladder::setTenant('tenant1');

        $user = User::factory()->create();

        $user->assignRole('admin', 'tenant1');
        $user->assignRole('editor', 'tenant2');

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('editor'));
    }

    public function test_unscoped_role_will_always_be_assigned()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_can_method_respects_global_scoping()
    {
        $user = User::factory()->create();
        $user->assignRole('admin', 'tenant1');

        Ladder::setTenant('tenant2');
        $this->assertFalse($user->hasPermission('read'));

        Ladder::setTenant('tenant1');
        $this->assertTrue($user->hasPermission('read'));
    }
}