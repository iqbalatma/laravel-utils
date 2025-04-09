<?php

namespace Iqbalatma\LaravelUtils\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DumpAPIException extends Exception
{
    public function __construct(public array $data)
    {
        parent::__construct($message = "", $code = 0, $previous = null);
    }


    #Post
    public function render(): JsonResponse
    {
        return response()->json($this->data);
    }
}
