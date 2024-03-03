<?php

namespace Iqbalatma\LaravelUtils\Contracts;

use Error;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseAPIResponse implements Responsable
{
    protected array $baseFormat;
    protected JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data;
    protected string $message;
    protected ResponseCodeInterface|null $responseCode;
    protected string|array|null $errors;
    protected Error|Exception|Throwable|null $exception;

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
    abstract protected function getResponseCode(): ResponseCodeInterface;


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
     * @return void
     */
    protected function setBaseFormat():void
    {
        $this->baseFormat = [
            "code" => $this->getResponseCode()->name,
            "message" => $this->getMessage(),
            "timestamp" => now()
        ];

        if ($this->errors) { #when errors are detected
            $this->baseFormat["errors"] = $this->errors;
        }

        if (self::getPayloadWrapper()) { #when payload wrapper are set, we will preserve the key
            $this->baseFormat[self::getPayloadWrapper()] = null;
        }

        if (
            ($this->exception instanceof Throwable) &&
            config("app.env") !== "production" &&
            config("app.debug") === true
        ) {
            $this->baseFormat["exception"] = [
                "name" => get_class($this->exception),
                "message" => $this->exception->getMessage(),
                "http_code" => $this->getHttpCode(),
                "code" => $this->exception->getCode(),
                "file" => $this->exception->getFile(),
                "line" => $this->exception->getLine(),
                "trace" => $this->exception->getTrace(),
            ];
        }
    }

    /**
     * @return string|null
     */
    protected static function getPayloadWrapper(): string|null
    {
        return config("utils.api_response.payload_wrapper");
    }
}
