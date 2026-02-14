<?php

return [
    'namesilo' => [
        'api_key' => env('NAMESILO_API_KEY'),
        'sandbox' => env('NAMESILO_SANDBOX', false),
    ],
    'cloudflare' => [
        'api_key' => env('CLOUDFLARE_API_KEY'),
        'email' => env('CLOUDFLARE_EMAIL'),
    ],
    'server_ip' => env('SERVER_IP', '84.32.109.211'),
];
