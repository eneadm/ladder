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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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