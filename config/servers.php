<?php

return [
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST'),
        'port' => intval(env('RABBITMQ_PORT', 5672)),
        'username' => env('RABBITMQ_USERNAME'),
        'password' => env('RABBITMQ_PASSWORD')
    ]
];
