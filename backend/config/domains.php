<?php

return [
    'namesilo' => [
        'api_key' => env('NAMESILO_API_KEY'),
        'sandbox' => env('NAMESILO_SANDBOX', false),
    ],
    'cloudflare' => [
        'api_key' => env('CLOUDFLARE_API_KEY'),
        'email' => env('CLOUDFLARE_EMAIL'),
        // Cloudflare for SaaS: zone ID of the fallback/origin zone
        'saas_zone_id' => env('CLOUDFLARE_SAAS_ZONE_ID'),
    ],
    'server_ip' => env('SERVER_IP', '84.32.109.211'),
];
