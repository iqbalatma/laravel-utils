<?php

namespace Iqbalatma\LaravelUtils\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;
use Iqbalatma\LaravelUtils\APIResponse;

/**
 * @property array formattedResponse
 */
trait FormatResponsePayloadTrait
{
    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForPaginator(): self
    {
        if (count($this->formattedResponse) === 0 && $this->getData() instanceof Paginator) {
            $meta = $this->getData()->toArray();
            unset($meta["data"]);

            if (self::getPayloadWrapper()) {
                $this->formattedResponse[self::getPayloadWrapper()] = array_merge(
                    [JsonResource::$wrap => $this->getData()->toArray()["data"]],
                    $meta,
                );
            } else {
                $this->formattedResponse = array_merge(
                    $this->formattedResponse,
                    [JsonResource::$wrap => $this->getData()->toArray()["data"]],
                    $meta,
                );
            }
        }
        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForArrayable(): self
    {
        if (count($this->formattedResponse) === 0 && $this->getData() instanceof Arrayable) {
            $source = [JsonResource::$wrap => $this->getData()->toArray()];
            if (self::getPayloadWrapper()) {
                $this->formattedResponse[self::getPayloadWrapper()] = $source;
            } else {
                $this->formattedResponse = $source;
            }
        }
        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForAbstractPaginator(): self
    {
        if (count($this->formattedResponse) === 0 && ($this->getData()?->resource ?? null) instanceof AbstractPaginator) {
            $meta = $this->getData()->resource->toArray();
            unset($meta["data"]);

            if (self::getPayloadWrapper()) {
                $this->formattedResponse[self::getPayloadWrapper()] = array_merge(
                    [JsonResource::$wrap => $this->getData()],
                    $meta,
                );
            } else {
                $this->formattedResponse = array_merge(
                    $this->formattedResponse,
                    [JsonResource::$wrap => $this->getData()],
                    $meta
                );
            }
        }

        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForResource(): self
    {
        if (count($this->formattedResponse) === 0) {
            if (self::getPayloadWrapper()) {
                $this->formattedResponse[self::getPayloadWrapper()] = $this->getData() ? [JsonResource::$wrap => $this->getData()] : null;
            } elseif (!self::getPayloadWrapper() && $this->getData()) {
                $this->formattedResponse[JsonResource::$wrap] = $this->getData();
            }
        }

        return $this;
    }
}
