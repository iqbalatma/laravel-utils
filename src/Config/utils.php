<?php

return [
    "target_enum_dir" => "app/Enums",
    "target_trait_dir" => "app/Traits",
    "target_abstract_dir" => "app/Contracts/Abstracts",
    "target_interface_dir" => "app/Contracts/Interfaces",
    "api_response" => [
        "payload_wrapper" => "payload",
    ],
    "is_show_debug" => env("APP_DEBUG", false)
];
