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

    /**
     * @param JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data
     * @param string|null $message
     * @param ResponseCodeInterface|null $responseCode
     * @param string|array|null $errors
     * @param Error|Exception|Throwable|null $exception
     * @return APIResponse
     */
    public function apiResponse(
        JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data = null,
        ?string                                                                                   $message = null,
        ?ResponseCodeInterface                                                                    $responseCode = null,
        string|array|null                                                                         $errors = null,
        Error|Exception|Throwable|null                                                            $exception = null
    ): APIResponse
    {
        return new APIResponse($data, $message, $responseCode, $errors, $exception);
    }
}
