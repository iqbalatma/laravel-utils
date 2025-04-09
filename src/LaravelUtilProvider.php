<?php

namespace Iqbalatma\LaravelUtils;

use Illuminate\Support\ServiceProvider;
use Iqbalatma\LaravelUtils\Console\Command\GenerateAbstractCommand;
use Iqbalatma\LaravelUtils\Console\Command\GenerateEnumCommand;
use Iqbalatma\LaravelUtils\Console\Command\GenerateInterfaceCommand;
use Iqbalatma\LaravelUtils\Console\Command\GenerateTraitCommand;
use Iqbalatma\LaravelUtils\Console\Command\PublishStubCommand;

class LaravelUtilProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/utils.php', 'utils');

        $this->publishes([
            __DIR__.'/Config/utils.php' => config_path('utils.php'),
        ], "config");
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateAbstractCommand::class,
                GenerateEnumCommand::class,
                GenerateInterfaceCommand::class,
                GenerateTraitCommand::class,
                PublishStubCommand::class,
            ]);
        }
    }
}
