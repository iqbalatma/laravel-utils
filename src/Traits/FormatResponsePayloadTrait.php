<?php

namespace Iqbalatma\LaravelUtils\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;
use Iqbalatma\LaravelUtils\APIResponse;

trait FormatResponsePayloadTrait
{
    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForPaginator(): self
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
            $this->formattedResponse = array_merge($this->getBaseFormat(), $mergeTarget);
        }
        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForArrayable(): self
    {
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
            $this->formattedResponse = array_merge($this->getBaseFormat(), $mergeTarget);
        }
        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForAbstractPaginator(): self
    {
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
            $this->formattedResponse = array_merge($this->getBaseFormat(), $mergeTarget);
        }

        return $this;
    }

    /**
     * @return APIResponse|FormatResponsePayloadTrait
     */
    protected function setResponseForResource():self
    {
        if (self::getPayloadWrapper()) {
            $this->formattedResponse = array_merge($this->getBaseFormat(), [
                self::getPayloadWrapper() => is_null($this->data) ? $this->data : [JsonResource::$wrap => $this->data]
            ]);
        } else {
            if ($this->data) {
                $this->formattedResponse = array_merge($this->getBaseFormat(), [JsonResource::$wrap => $this->data]);
            } else {
                $this->formattedResponse = array_merge($this->getBaseFormat());
            }
        }

        return $this;
    }
}
