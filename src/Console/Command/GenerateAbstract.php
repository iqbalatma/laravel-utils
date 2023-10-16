<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Iqbalatma\LaravelUtils\Traits\MakeCommand;

class GenerateAbstract extends Command
{
    use MakeCommand;

    protected const STUB_FILE_PATH = __DIR__ . "/../Stubs/abstract.stub";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:abstract {name : abstract name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new abstract class';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->prepareMakeCommand(app_path("Contracts/Abstracts"), "App\\Contracts\\Abstracts")
            ->generateFromStub();
    }

    protected function getStubVariables(): array
    {
        return [
            "CLASS_NAME" => $this->getClassName(),
            "NAMESPACE" => $this->getNamespace()
        ];
    }
}
