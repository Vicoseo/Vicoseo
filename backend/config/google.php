<?php

return [
    'service_account_path' => env('GOOGLE_SERVICE_ACCOUNT_PATH', storage_path('app/google/service-account.json')),

    'analytics' => [
        'cache_ttl' => 14400, // 4 hours
    ],

    'search_console' => [
        'cache_ttl' => 14400, // 4 hours
    ],
];
