<?php

namespace Iqbalatma\LaravelUtils;

use Error;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class APIResponse implements Responsable
{
    protected array $baseFormat;
    protected const PAYLOAD_WRAPPER = "payload";

    public function __construct(
        protected JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data = null,
        protected string|null                                                                               $message = null,
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

        $this->baseFormat["payload"] = null;

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
                    } elseif (str_starts_with($httpCode, "4")) {
                        return ResponseCode::ERR_BAD_REQUEST();
                    } else {
                        return ResponseCode::ERR_UNKNOWN();
                    }
                }

                return ResponseCode::ERR_UNKNOWN();
            }

            return ResponseCode::SUCCESS();
        }

        return $this->responseCode;
    }


    /**
     * @return JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null
     */
    protected function getData(): JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null
    {
        return $this->data;
    }


    /**
     * @return string
     */
    protected function getMessage(): string
    {
        return $this->message ?? "";
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
    protected function getBaseFormat(): array
    {
        return $this->baseFormat;
    }


    /**
     * @return array
     */
    protected function getFormattedResponse(): array
    {
        if ($this->getData() instanceof Paginator) {
            $meta = $this->getData()->toArray();
            unset($meta["data"]);

            return array_merge($this->getBaseFormat(), [
                self::PAYLOAD_WRAPPER => array_merge(
                    [JsonResource::$wrap => $this->getData()->toArray()["data"]],
                    $meta,
                )
            ]);
        }

        if ($this->getData() instanceof Arrayable) {
            return array_merge($this->getBaseFormat(), [
                self::PAYLOAD_WRAPPER => [
                    JsonResource::$wrap => $this->getData()->toArray()
                ]
            ]);
        }

        if (($this->getData()?->resource ?? null) instanceof AbstractPaginator) {
            $meta = $this->getData()->resource->toArray();
            unset($meta["data"]);

            return array_merge($this->getBaseFormat(), [
                self::PAYLOAD_WRAPPER => array_merge(
                    [JsonResource::$wrap => $this->getData()],
                    $meta,
                )
            ]);
        }

        return array_merge($this->getBaseFormat(), [
            self::PAYLOAD_WRAPPER => is_null($this->data) ? $this->data : [JsonResource::$wrap => $this->data]
        ]);
    }

    /**
     * @param $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        return response()->json($this->getFormattedResponse(), $this->getHttpCode());
    }
}
