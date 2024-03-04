<?php

namespace Iqbalatma\LaravelUtils;

use Error;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Iqbalatma\LaravelUtils\Contracts\BaseAPIResponse;
use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Iqbalatma\LaravelUtils\Traits\FormatResponsePayloadTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class APIResponse extends BaseAPIResponse
{
    use FormatResponsePayloadTrait;
    public function __construct(
        JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data = null,
        string                                                                                    $message = "",
        ResponseCodeInterface|null                                                                $responseCode = null,
        string|array|null                                                                         $errors = null,
        Error|Exception|Throwable|null                                                            $exception = null
    )
    {
        $this->data = $data;
        $this->message = $message;
        $this->responseCode = $responseCode;
        $this->errors = $errors;
        $this->exception = $exception;

        $this->setBaseFormat()
            ->setFormattedResponse();
    }


    /**
     * @return BaseAPIResponse
     */
    protected function setBaseFormat(): self
    {
        $this->baseFormat = [
            "code" => $this->getCode()->name,
            "message" => $this->getMessage(),
            "timestamp" => now()
        ];

        if ($this->errors) { #when errors are detected, mostly for validation error
            $this->baseFormat["errors"] = $this->errors;
        }

        if (self::getPayloadWrapper()) { #when payload wrapper is set, we will preserve the key
            $this->baseFormat[self::getPayloadWrapper()] = null;
        }

        if ($this->exception instanceof Throwable && config("utils.is_show_debug")) {
            $this->baseFormat["user_request"] = [
                'ip_address' => request()->getClientIp(),
                'base_url' => request()->getBaseUrl() ?? null,
                'path' => request()->getUri() ?? null,
                'params' => request()->getQueryString(),
                'origin' => request()->ip() ?? null,
                'method' => request()->getMethod() ?? null,
                'header' => request()->headers->all() ?? null,
                'body' => request()->all() ?? null,
            ];
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

        return $this;
    }

    /**
     * @return void
     */
    protected function setFormattedResponse(): void
    {
        $this->setResponseForPaginator()
            ->setResponseForArrayable()
            ->setResponseForAbstractPaginator()
            ->setResponseForResource();
    }
}
