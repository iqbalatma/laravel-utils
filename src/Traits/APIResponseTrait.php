<?php

namespace Iqbalatma\LaravelUtils\Traits;

use Error;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Iqbalatma\LaravelUtils\APIResponse;
use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Throwable;

trait APIResponseTrait
{
    protected array $responseMessages;

    /**
     * Use to get response message
     *
     * @param string $context
     * @return string
     */
    public function getResponseMessage(string $context): string
    {
        return $this->responseMessages[$context];
    }
}
