<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DapCode License & Global Access Control Configuration
    |--------------------------------------------------------------------------
    */

    // Storage path for license runtime files (strictly outside public web root)
    'storage_path' => storage_path('app/dapcode'),

    'files' => [
        'installation'  => storage_path('app/dapcode/.installation'),
        'license'       => storage_path('app/dapcode/.license'),
        'license_state' => storage_path('app/dapcode/.license-state'),
        'public_key'    => storage_path('app/dapcode/public_key.pem'),
    ],

    // All available application modules requiring authorization
    'modules' => [
        'dashboard',
        'profile',
        'education',
        'commerce',
        'research',
        'career',
        'activity',
        'media',
        'achievement',
        'certification',
        'interest',
        'project',
        'setting',
    ],

    // Routes explicitly excluded from license enforcement (Only activation & static assets)
    'excluded_routes' => [
        'dapcode/*',
        'dapcode',
        'build/*',
        'assets/*',
        'favicon.ico',
        '_debugbar/*',
    ],

    // Remote License Authority Endpoint (Configurable)
    'license_server_url' => env('DAPCODE_LICENSE_SERVER_URL', 'https://license.dapcode.com/api/v1'),

    // Offline / Network Failure Grace Period (in days)
    'offline_grace_period_days' => 14,
];
