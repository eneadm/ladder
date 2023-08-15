<?php

namespace Ladder;

use Illuminate\Support\ServiceProvider;

class LadderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureCommands();
    }

    public function configurePublishing(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2023_08_12_000000_create_user_role_table.php');
    }

    public function configureCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}