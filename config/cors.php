<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://192.168.1.100:5173',
        'http://192.168.1.101:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // OBRIGATÓRIO para Sanctum SPA — permite envio de cookies
    'supports_credentials' => true,
];
