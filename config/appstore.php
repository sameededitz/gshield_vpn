<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Apple App Store Configuration
    |--------------------------------------------------------------------------
    */

    'issuer_id' => env('APPLE_ISSUER_ID'),
    'key_id' => env('APPLE_KEY_ID'),
    'private_key_path' => env('APPLE_PRIVATE_KEY_PATH'),
    'bundle_id' => env('APPLE_BUNDLE_ID'),
    'environment' => env('APPSTORE_ENVIRONMENT', 'sandbox'),
    'shared_secret' => env('APPSTORE_SHARED_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */

    'endpoints' => [
        'production' => 'https://api.storekit.itunes.apple.com',
        'sandbox' => 'https://api.storekit-sandbox.itunes.apple.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Apple Public Keys
    |--------------------------------------------------------------------------
    */

    'public_keys_url' => 'https://appleid.apple.com/auth/keys',

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'verify_signature' => env('APPSTORE_VERIFY_SIGNATURE', true),
        'log_all_notifications' => env('APPSTORE_LOG_NOTIFICATIONS', true),
    ],
];