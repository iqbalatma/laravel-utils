<?php

namespace Iqbalatma\LaravelUtils;

use Illuminate\Support\ServiceProvider;
use Iqbalatma\LaravelUtils\Console\Command\GenerateAbstract;
use Iqbalatma\LaravelUtils\Console\Command\GenerateEnum;
use Iqbalatma\LaravelUtils\Console\Command\GenerateInterface;
use Iqbalatma\LaravelUtils\Console\Command\GenerateTrait;
use Iqbalatma\LaravelUtils\Console\Command\PublishStub;

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
                GenerateAbstract::class,
                GenerateEnum::class,
                GenerateInterface::class,
                GenerateTrait::class,
                PublishStub::class,
            ]);
        }
    }
}
