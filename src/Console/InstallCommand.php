<?php

namespace Ladder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'ladder:install';

    protected $description = 'Create the Ladder scaffolding.';

    public function handle(): bool
    {
        // Service Providers...
        copy(__DIR__.'/../../stubs/app/Providers/LadderServiceProvider.php', app_path('Providers/LadderServiceProvider.php'));
        $this->installServiceProviderAfter('RouteServiceProvider', 'LadderServiceProvider');

        $this->line('');
        $this->components->info('Ladder scaffolding installed successfully.');

        return true;
    }

    protected function installServiceProviderAfter($after, $name): void
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\'.$name.'::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\'.$after.'::class,',
                'App\\Providers\\'.$after.'::class,'.PHP_EOL.'        App\\Providers\\'.$name.'::class,',
                $appConfig
            ));
        }
    }
}