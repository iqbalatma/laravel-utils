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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class APIResponse extends BaseAPIResponse
{
    public function __construct(
        protected JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data = null,
        protected string                                                                                    $message = "",
        protected ResponseCodeInterface|null                                                                $responseCode = null,
        protected string|array|null                                                                         $errors = null,
        protected Error|Exception|Throwable|null                                                            $exception = null
    )
    {
        $this->baseFormat = [
            "code" => $this->getResponseCode()->name,
            "message" => $this->getMessage(),
            "timestamp" => now()
        ];

        if (!is_null($this->errors)) {
            $this->baseFormat["errors"] = $this->errors;
        }

        if (self::getPayloadWrapper()) {
            $this->baseFormat[self::getPayloadWrapper()] = null;
        }

        if (
            ($this->exception instanceof \Throwable) &&
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
     * @return ResponseCodeInterface
     */
    protected function getResponseCode(): ResponseCodeInterface
    {
        if (is_null($this->responseCode)) {
            if ($this->exception) {
                if ($this->exception instanceof HttpExceptionInterface) {
                    $httpCode = (string)$this->exception->getStatusCode();
                    if (str_starts_with($httpCode, "5")) {
                        return ResponseCode::ERR_INTERNAL_SERVER_ERROR();
                    }

                    if (str_starts_with($httpCode, "4")) {
                        return ResponseCode::ERR_BAD_REQUEST();
                    }

                    return ResponseCode::ERR_UNKNOWN();
                }

                return ResponseCode::ERR_UNKNOWN();
            }

            return ResponseCode::SUCCESS();
        }

        return $this->responseCode;
    }


    /**
     * @return int
     */
    protected function getHttpCode(): int
    {
        if ($this->exception instanceof HttpExceptionInterface) {
            return $this->exception->getStatusCode();
        }
        return $this->getResponseCode()->httpCode ?? Response::HTTP_INTERNAL_SERVER_ERROR;
    }


    /**
     * @return array
     */
    protected function getFormattedResponse(): array
    {
        if ($this->getData() instanceof Paginator) {
            $meta = $this->getData()->toArray();
            unset($meta["data"]);

            if (self::getPayloadWrapper()) {
                $mergeTarget = [
                    self::getPayloadWrapper() => array_merge(
                        [JsonResource::$wrap => $this->getData()->toArray()["data"]],
                        $meta,
                    )
                ];
            } else {
                $mergeTarget = array_merge(
                    [JsonResource::$wrap => $this->getData()->toArray()["data"]],
                    $meta,
                );
            }
            return array_merge($this->getBaseFormat(), $mergeTarget);
        }

        if ($this->getData() instanceof Arrayable) {
            if (self::getPayloadWrapper()) {
                $mergeTarget = [
                    self::getPayloadWrapper() => [
                        JsonResource::$wrap => $this->getData()->toArray()
                    ]
                ];
            } else {
                $mergeTarget = [
                    JsonResource::$wrap => $this->getData()->toArray()
                ];
            }
            return array_merge($this->getBaseFormat(), $mergeTarget);
        }

        if (($this->getData()?->resource ?? null) instanceof AbstractPaginator) {
            $meta = $this->getData()->resource->toArray();
            unset($meta["data"]);

            if (self::getPayloadWrapper()) {
                $mergeTarget = [
                    self::getPayloadWrapper() => array_merge(
                        [JsonResource::$wrap => $this->getData()],
                        $meta,
                    )
                ];
            } else {
                $mergeTarget = array_merge(
                    [JsonResource::$wrap => $this->getData()],
                    $meta,
                );
            }
            return array_merge($this->getBaseFormat(), $mergeTarget);
        }

        if (self::getPayloadWrapper()) {
            $mergeTarget = [
                self::getPayloadWrapper() => is_null($this->data) ? $this->data : [JsonResource::$wrap => $this->data]
            ];
            return array_merge($this->getBaseFormat(), $mergeTarget);
        } else {
            if ($this->data) {
                return array_merge($this->getBaseFormat(), [JsonResource::$wrap => $this->data]);
            } else {
                return array_merge($this->getBaseFormat());
            }
        }

    }
}
