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
        $files = [
            '2023_08_12_000000_create_model_role_table.php',
            '2014_10_12_000000_create_folders_table.php',
        ];

        $this->loadMigrationsFrom(collect($files)->map(fn ($file) => __DIR__.'/../database/migrations/'.$file)->toArray());
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
