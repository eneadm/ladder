<?php

namespace Ladder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Ladder\Ladder;
use Symfony\Component\Console\Helper\TableCell;

class Show extends Command
{
    protected $signature = 'ladder:show
            {style=default : The display style (default|borderless|compact|box)}';

    protected $description = 'Show a table of roles and permissions';

    /**
     * Make the table header.
     * @param \Illuminate\Support\Collection $roles
     * @return array
     */
    private function makeTableHeader(Collection $roles): array
    {
        return $roles->pluck('key')->prepend(new TableCell(''))->toArray();
    }

    /**
     * Make the table body.
     * @param \Illuminate\Support\Collection $permissions
     * @param \Illuminate\Support\Collection $roles
     * @return array
     */
    private function makeTableBody(Collection $permissions, Collection $roles): array
    {
        return $permissions->map(function ($permission) use ($roles) {
            return $this->getPermissionRow($permission, $roles);
        })->toArray();
    }

    /**
     * Get the permissions from the roles.
     * @param \Illuminate\Support\Collection $roles
     * @return \Illuminate\Support\Collection
     */
    private function getPermissions(Collection $roles): Collection
    {
        return $roles
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get the permission row.
     * @param string $permission
     * @param \Illuminate\Support\Collection $roles
     * @return \Illuminate\Support\Collection
     */
    private function getPermissionRow(string $permission, Collection $roles): Collection
    {
        return $roles->map(function ($role) use ($permission) {
            return $this->hasPermission($role, $permission) ? '✔' : '✖';
        })->prepend($permission);
    }

    /**
     * Check if the role has the permission.
     * @param mixed $role
     * @param mixed $permission
     * @return bool
     */
    private function hasPermission($role, $permission): bool
    {
        return collect($role->permissions)->contains($permission);
    }

    public function handle()
    {
        $style = $this->argument('style');
        $ladderClass = app(Ladder::class);
        $roles = collect($ladderClass::$roles);
        $permissions = $this->getPermissions($roles);
        $header = $this->makeTableHeader($roles);
        $body = $this->makeTableBody($permissions, $roles);

        $this->table($header, $body, $style);

        return Command::SUCCESS;
    }
}
