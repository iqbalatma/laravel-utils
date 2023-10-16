<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Iqbalatma\LaravelUtils\Traits\MakeCommand;

class GenerateInterface extends Command
{
    use MakeCommand;
    protected const STUB_FILE_PATH = __DIR__ . "/../Stubs/interface.stub";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name : interface name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $this->prepareMakeCommand(app_path("Contracts/Interfaces"), "App\\Contracts\\Interfaces")
            ->generateFromStub();
    }

    protected function getStubVariables(): array
    {
        return [
            "NAMESPACE" => $this->getNamespace(),
            "CLASS_NAME" => $this->getClassName()
        ];
    }
}
