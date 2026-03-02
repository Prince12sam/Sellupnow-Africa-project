<?php

return [
    'enabled' => (bool) env('LISTOCEAN_ADAPTER_ENABLED', false),
    'base_url' => env('LISTOCEAN_BASE_URL', ''),
    'admin_prefix' => env('LISTOCEAN_ADMIN_PREFIX', '/api/v1/admin'),
    'token' => env('LISTOCEAN_ADMIN_TOKEN', ''),
    'forward_bearer' => (bool) env('LISTOCEAN_FORWARD_BEARER', true),
    'timeout' => (int) env('LISTOCEAN_TIMEOUT', 15),
];
