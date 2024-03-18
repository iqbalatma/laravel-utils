# Laravel Utils
Laravel utils is collection of class, helper, command console for help you to efficiency your development time. 
Here we have command console for generate enum, abstract, interface, or trait

## Config
Target dir is where generated file root path. Change this target dir location by your preference.
```php
return [
    "target_enum_dir" => "app/Enums",
    "target_trait_dir" => "app/Traits",
    "target_abstract_dir" => "app/Contracts/Abstracts",
    "target_interface_dir" => "app/Contracts/Interfaces",
];
```

## Generate File Command

You can generate enum with this utils
You can also create backed enum, with string or int as type
### Enum
```shell
php artisan make:enum Gender
php artisan make:enum Gender --type=string
```

You can generate trait with this command
### Trait
```shell
php artisan make:trait HasInstitution
```

You can generate abstract with this command
### Abstract
```shell
php artisan make:abstract BaseService
```


You can generate interface with this command
### Interface
```shell
php artisan make:inteface IRouter
```

## Publish Stub File
In some condition you may publish stub file and modify template. 
```shell
php artisan utils:publish-stub
```

## Formatting API Response
You can format your api response using this feature. For example :
```php
<?php

namespace App\Http\Controllers\API\Internal\Management;

use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\Permission;
use App\Http\Resources\Internal\Management\Permissions\PermissionResourceCollection;

class PermissionController extends ApiController
{
    /**
     * @return APIResponse
     */
    public function index(): APIResponse
    {
        $data = Permission::all();

        return $this->response(
            new PermissionResourceCollection($data),
            "Get all data permission successfully"
        );
    }
}
```
Description :
- First argument is for data, you can pass paginator, string, array, resource, and null.
- Second argument is for message, data type is string
- Third argument is response code. You can see response code in Iqbalatma/LaravelUtils/ResponseCode
- Forth argument is errors, mostly used for validation errors.
- Fifth argument is for exception, mostly used for mapping execption.

The response would be 
```json
{
    "rc": "SUCCESS",
    "message": "Get all data permission successfully",
    "timestamp": "2023-10-26T23:49:47.387526Z",
    "payload": {
        "data": [
          {
            "id": "99ffccb6-b375-4cf5-9f4c-b6824fabeab3",
            "name": "can show all user",
            "guard_name": "api"
          },
          {
            "id": "99ffccb6-b375-4cf5-9f4c-b6824fabeab4",
            "name": "can show all products",
            "guard_name": "api"
          }
      ]
    }
}
```

You can customize response code (rc) using third argument.
You can override to add response code of this class. Just override the class and override mapHttpCode() function.
For example you can add response code for ERR_NOT_FOUND()

```php
<?php

namespace App\Services\V1;

use Iqbalatma\LaravelUtils\Interfaces\ResponseCodeInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method static ResponseCodeInterface ERR_NOT_FOUND()
 */
class ResponseCode extends \Iqbalatma\LaravelUtils\ResponseCode
{
    protected const ERR_NOT_FOUND = "ERR_NOT_FOUND";


    /**
     * @return void
     */
    protected function mapHttpCode(): void
    {
        $this->httpCode = match ($this->name) {
            self::ERR_NOT_FOUND => Response::HTTP_NOT_FOUND,
            default => null
        };

        if ($this->httpCode === null) {
            parent::mapHttpCode();
        }
    }
}
```

This is how to handle exception and customize response format:
```php
$this->renderable(function (NotFoundHttpException $e) {
    if (request()->expectsJson()) {
        return new APIResponse(
            null,
            message: $e->getMessage(),
            responseCode: ResponseCode::ERR_NOT_FOUND(),
            exception: $e
        );
    }
});
```


