<?php

namespace Iqbalatma\LaravelUtils;

use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method static ResponseCodeInterface SUCCESS()
 * @method static ResponseCodeInterface CREATED()
 * @method static ResponseCodeInterface ERR_UNAUTHENTICATED()
 * @method static ResponseCodeInterface ERR_VALIDATION()
 * @method static ResponseCodeInterface ERR_FORBIDDEN()
 * @method static ResponseCodeInterface ERR_ENTITY_NOT_FOUND()
 * @method static ResponseCodeInterface ERR_INTERNAL_SERVER_ERROR()
 * @method static ResponseCodeInterface ERR_BAD_REQUEST()
 * @method static ResponseCodeInterface ERR_UNKNOWN()
 */
class ResponseCode implements ResponseCodeInterface
{
    public string $value;
    public string $name;
    public string|null $httpCode;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->value = constant(get_class($this) . '::' . $name);
    }

    protected const SUCCESS = "SUCCESS";
    protected const CREATED = "CREATED";
    protected const ERR_UNAUTHENTICATED = "ERR_UNAUTHENTICATED";
    protected const ERR_VALIDATION = "ERR_VALIDATION";
    protected const ERR_FORBIDDEN = "ERR_FORBIDDEN";
    protected const ERR_ENTITY_NOT_FOUND = "ERR_ENTITY_NOT_FOUND";
    protected const ERR_INTERNAL_SERVER_ERROR = "ERR_INTERNAL_SERVER_ERROR";
    protected const ERR_BAD_REQUEST = "ERR_BAD_REQUEST";
    protected const ERR_UNKNOWN = "ERR_UNKNOWN";

    public static function __callStatic(string $name, array $arguments):ResponseCodeInterface
    {
        $instance = new static($name);
        $instance->mapHttpCode();
        return $instance;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if ($name === "value") {
            return $this->{$this->name};
        }

        throw new \BadMethodCallException();
    }


    /**
     * @return void
     */
    protected function mapHttpCode(): void
    {
        $this->httpCode = match ($this->name) {
             self::SUCCESS => Response::HTTP_OK,
             self::CREATED => Response::HTTP_CREATED,
             self::ERR_VALIDATION => Response::HTTP_UNPROCESSABLE_ENTITY,
             self::ERR_FORBIDDEN => Response::HTTP_FORBIDDEN,
             self::ERR_UNAUTHENTICATED => Response::HTTP_UNAUTHORIZED,
             self::ERR_ENTITY_NOT_FOUND => Response::HTTP_NOT_FOUND,
             self::ERR_BAD_REQUEST => Response::HTTP_BAD_REQUEST,
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }
}
