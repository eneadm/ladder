<?php

namespace Tests;

use Ladder\Ladder;

class LadderTest extends OrchestraTestCase
{
    public function test_roles_can_be_registered()
    {
        Ladder::$permissions = [];
        Ladder::$roles = [];

        Ladder::role('admin', 'Admin', [
            'read',
            'create',
            'update',
            'delete',
        ])->description('Some admin description');

        $this->assertTrue(Ladder::hasPermissions());

        $this->assertEquals([
            'create',
            'delete',
            'read',
            'update',
        ], Ladder::$permissions);
    }

    public function test_roles_can_be_json_serialized()
    {
        Ladder::$permissions = [];
        Ladder::$roles = [];

        $role = Ladder::role('admin', 'Admin', [
            'read',
            'create',
        ])->description('Some admin description');

        $serialized = $role->jsonSerialize();

        $this->assertArrayHasKey('key', $serialized);
        $this->assertArrayHasKey('name', $serialized);
        $this->assertArrayHasKey('description', $serialized);
        $this->assertArrayHasKey('permissions', $serialized);
    }
}
