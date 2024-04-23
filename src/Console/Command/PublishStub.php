<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishStub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:publish-stub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish stub on generate command abstract, interface, enum, and trait';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info("Publishing generate command stub");

        $fileSystem = new Filesystem();

        if (!$fileSystem->isDirectory(base_path("/stubs/laravel-utils"))){
            $fileSystem->makeDirectory(base_path("/stubs/laravel-utils"), recursive: true);
        }

        foreach ($fileSystem->allFiles(__DIR__ . "/../Stubs",) as $file) {
            $fileSystem->copy($file->getRealPath(), base_path("/stubs/laravel-utils/").$file->getFilename());
        }

        $this->info("Stub asset published");
    }
}
