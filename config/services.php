<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'perfios' => [
        'base_url' => env('BASE_URL'),
        'user_name' => env('PERFIOS_USERNAME'),
        'client_id' => env('PERFIOS_CLIENT_ID'),
        'client_password' => env('PERFIOS_PASSWORD'),
    ],
    'karza' => [
        'base_url' => env('BASE_URL_KARZA'),
        'user_name' => env('KARZA_USERNAME'),
        'client_id' => env('KARZA_CLIENT_ID'),
        'client_password' => env('KARZA_PASSWORD'),
        'api_key' => env('KARZA_API_KEY'),
        'api_key_gst_itr' => env('KARZA_API_KEY_GSTITR'),
    ],
    'SETU' => [
        'SETU_BASE_URL' => env('SETU_BASE_URL'),
        'SETU_CLIENT_ID' => env('SETU_CLIENT_ID'),
        'SETU_CLIENT_SECRET' => env('SETU_CLIENT_SECRET'),
        'SETU_PRODUCT_INSTANCE_ID' => env('SETU_PRODUCT_INSTANCE_ID'),

    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],
    'otp' => [
        'provider' => env('OTP_SMS_PROVIDER', 'your_provider'),

        'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 5),

        'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

        'resend_seconds' => (int) env('OTP_RESEND_SECONDS', 30),
    ],

    'sms' => [
        'url' => env('SMS_API_URL'),
        'key' => env('SMS_API_KEY'),
        'secret' => env('SMS_API_SECRET'),
        'sender_id' => env('SMS_SENDER_ID'),
        'template_id' => env('SMS_OTP_TEMPLATE_ID'),
    ],
];
