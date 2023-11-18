<?php

namespace Iqbalatma\LaravelUtils;

use Illuminate\Support\ServiceProvider;
use Iqbalatma\LaravelUtils\Console\Command\GenerateAbstract;
use Iqbalatma\LaravelUtils\Console\Command\GenerateEnum;
use Iqbalatma\LaravelUtils\Console\Command\GenerateInterface;
use Iqbalatma\LaravelUtils\Console\Command\GenerateTrait;

class LaravelUtilProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/utils.php', 'utils');

        $this->publishes([
            __DIR__.'/Console/Stubs/enum.stub' => base_path('stubs/laravel-utils/enum.stub'),
        ], 'stub_enum');
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
            ]);
        }

        $this->publishes([
            __DIR__.'/Config/utils.php' => config_path('utils.php'),
        ]);
    }
}
