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

