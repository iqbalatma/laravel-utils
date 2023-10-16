<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Iqbalatma\LaravelUtils\Traits\MakeCommand;

class GenerateTrait extends Command
{
    use MakeCommand;
    protected const STUB_FILE_PATH = __DIR__ . "/../Stubs/trait.stub";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name : trait name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $this->prepareMakeCommand(app_path("Traits"), "App\\Traits")
            ->generateFromStub();
    }

    protected function getStubVariables(): array
    {
        return [
            "CLASS_NAME" => $this->getClassName(),
            "NAMESPACE" => $this->getNamespace(),
        ];
    }
}
