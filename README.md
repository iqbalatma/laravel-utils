# Laravel Utils
Laravel utils is collection of class, helper, command console for help you to efficiency your development time. 
Here we have command console for generate enum, abstract, interface, or trait

## Config
- With *target_enum_dir* you can modify where is root of enum target path location
```php
return [
    "target_enum_dir" => "app/Enums",
];

```

## Generate Enum
You can generate enum with this utils
```shell
php artisan make:enum Gender
```

You can also create backed enum, with string or int as type
```shell
php artisan make:enum Gender --type=string
```
