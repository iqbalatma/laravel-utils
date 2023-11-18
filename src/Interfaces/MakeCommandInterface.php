<?php

namespace Iqbalatma\LaravelUtils\Interfaces;

interface MakeCommandInterface
{
    public function getStubVariables(): array;
    public function getStubContent(): string;
}
