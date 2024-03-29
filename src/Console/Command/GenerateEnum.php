<?php

namespace Iqbalatma\LaravelUtils\Console\Command;

use Illuminate\Console\Command;
use Iqbalatma\LaravelUtils\Interfaces\MakeCommandInterface;
use Iqbalatma\LaravelUtils\Traits\MakeCommand;

class GenerateEnum extends Command implements MakeCommandInterface
{
    use MakeCommand;

    protected const STUB_FILE_PATH = __DIR__ . "/../Stubs/enum.stub";
    protected const AVAILABLE_TYPES = [
      "string", "int"
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name : enum name} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->prepareMakeCommand(targetPath: config("utils.target_enum_dir", "app/Enums"))
            ->generateFromStub();
    }

    /**
     * @return string
     */
    public function getStubContent(): string
    {
        /**
         * using enum stub from published stub if exists, and default stub if not exists
         */
        $stubContent = $this->filesystem->exists(base_path("stubs/laravel-utils/enum.stub")) ?
            file_get_contents("stubs/laravel-utils/enum.stub") :
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
     * @return string
     */
    private function getEnumType(): string
    {
        if ($type = $this->option("type")) {
            if (in_array($type, self::AVAILABLE_TYPES)) {
                return ":$type";
            }

            $this->error("Type is invalid");
            $this->info("Available Type : ");
            foreach (self::AVAILABLE_TYPES as $type){
                $this->info("-$type");
            }
            die();
        }
        return "";
    }

    /**
     * @return array
     */
    public function getStubVariables(): array
    {
        return [
            "ENUM_NAME" => $this->getClassName(),
            "NAMESPACE" => $this->getNamespace(),
            "TYPE" => $this->getEnumType()
        ];
    }
}
