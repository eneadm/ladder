<?php

namespace Tests;

use Ladder\Ladder;

class CommandTest extends OrchestraTestCase
{
    public function test_it_can_show_permission_table()
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
        ])->description('Editor users have the ability to read, create, and update.');

        $this->artisan('ladder:show')
            ->expectsTable([
                '',
                'admin',
                'editor',
            ], [
                ['create', '✔', '✔'],
                ['delete', '✔', '✖'],
                ['read', '✔', '✔'],
                ['update', '✔', '✔'],
            ]);
    }
}
