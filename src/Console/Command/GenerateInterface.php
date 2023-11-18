<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Iqbalatma\LaravelUtils\Interfaces\MakeCommandInterface;
use Iqbalatma\LaravelUtils\Traits\MakeCommand;

class GenerateInterface extends Command implements MakeCommandInterface
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
    public function handle(): void
    {
        $this->prepareMakeCommand(config("utils.target_interface_dir", "app/Contracts/Interface"))
            ->generateFromStub();
    }


    /**
     * @return string
     */
    public function getStubContent(): string
    {
        /**
         * using interface stub from published stub if exists, and default stub if not exists
         */
        $stubContent = $this->filesystem->exists(base_path("stubs/laravel-utils/interface.stub")) ?
            file_get_contents("stubs/laravel-utils/interface.stub") :
            file_get_contents(self::STUB_FILE_PATH);

        /**
         * replacing stub placeholder with variables
         */
        foreach ($this->getStubVariables() as $key => $variable) {
            $stubContent = str_replace("*$key*", $variable, $stubContent);
        }

        return $stubContent;
    }

    /**
     * @return array
     */
    public function getStubVariables(): array
    {
        return [
            "NAMESPACE" => $this->getNamespace(),
            "CLASS_NAME" => $this->getClassName()
        ];
    }
}
