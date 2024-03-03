<?php

namespace Iqbalatma\LaravelUtils\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseAPIResponse implements Responsable
{
    protected array $baseFormat;

    /**
     * @param $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        return response()->json($this->getFormattedResponse(), $this->getHttpCode());
    }

    /**
     * @return array
     */
    abstract protected function getFormattedResponse(): array;

    /**
     * @return int
     */
    abstract protected function getHttpCode(): int;


    /**
     * @return array
     */
    protected function getBaseFormat(): array
    {
        return $this->baseFormat;
    }


    /**
     * @return string
     */
    protected function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null
     */
    protected function getData(): JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    protected static function getPayloadWrapper():string|null
    {
        return config("utils.api_response.payload_wrapper");
    }
}
