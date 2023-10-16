<?php

namespace Iqbalatma\LaravelUtils\Traits;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

trait MakeCommand
{
    private string $argumentName;
    private string $className;
    private string $targetPath;
    private string $filename;
    private string $namespace;
    private Filesystem $filesystem;

    /**
     * @return Command
     */
    public function getConsoleInstance(): Command
    {
        return $this;
    }

    /**
     * @return MakeCommand
     */
    protected function setArgumentName(): static
    {
        $this->argumentName = ucwords($this->getConsoleInstance()->argument("name"));
        return $this;
    }

    /**
     * @return string
     */
    protected function getArgumentName(): string
    {
        return $this->argumentName;
    }

    /**
     * @return MakeCommand
     */
    protected function setClassName(): static
    {
        $this->className = ucwords(last(explode("/", $this->getArgumentName())));
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $targetPath
     * @return MakeCommand
     */
    protected function setTargetPath(string $targetPath): static
    {
        $this->targetPath = $targetPath;

        /**
         * example: if argument only Role
         * it's meant no need to nest path
         * but if argument is nested, we will create new directory with nest path
         */
        if (($dirname = dirname($this->getArgumentName())) !== ".") {
            $dirname = ucwords($dirname);
            $this->targetPath .= "/$dirname";
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getTargetPath(): string
    {
        return $this->targetPath;
    }

    /**
     * @return MakeCommand
     */
    protected function setFilename(): static
    {
        $this->filename = $this->getTargetPath() . "/" . $this->getClassName() . ".php";
        return $this;
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $defaultNamespace
     * @return MakeCommand
     */
    private function setNamespace(string $defaultNamespace): static
    {
        $this->namespace = $defaultNamespace;
        if (($dirname = dirname($this->getArgumentName())) !== ".") {
            $dirname = ucwords(str_replace("/", "\\", $dirname));
            $this->namespace .= "\\$dirname";
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return MakeCommand
     */
    protected function makeDirectory(): static
    {
        $this->filesystem = new Filesystem();

        if (!$this->filesystem->isDirectory($this->getTargetPath())) {
            $this->filesystem->makeDirectory($this->getTargetPath());
        }
        return $this;
    }

    /**
     * @return array
     */
    protected abstract function getStubVariables(): array;

    /**
     * @return void
     */
    protected function generateFromStub(): void
    {
        if (!$this->filesystem->exists($this->getFilename())) {
            $this->filesystem->put($this->getFilename(), $this->getFileContent(self::STUB_FILE_PATH));
            $this->info("Create " . $this->getClassName() . " successfully");
        } else {
            $this->error($this->className . " already exists");
        }
    }

    /**
     * @param string $stubPath
     * @return string
     */
    protected function getFileContent(string $stubPath): string
    {
        $stubContent = file_get_contents($stubPath);

        foreach ($this->getStubVariables() as $key => $variable) {
            $stubContent = str_replace("*$key*", $variable, $stubContent);
        }
        return $stubContent;
    }

    /**
     * @param string $targetPath
     * @param string $defaultNamespace
     * @return $this
     */
    protected function prepareMakeCommand(string $targetPath, string $defaultNamespace): static
    {
        $this->setArgumentName()
            ->setClassName()
            ->setTargetPath($targetPath)
            ->setFilename()
            ->setNamespace($defaultNamespace)
            ->makeDirectory();

        return $this;
    }
}
