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

    'ml_service' => [
        'url' => env('ML_SERVICE_URL', 'http://127.0.0.1:5000'),
        'timeout' => env('ML_SERVICE_TIMEOUT', 3),
        'mode' => env('ML_MODE', 'php'),          // 'php' or 'python'
        'key' => env('ML_SERVICE_KEY', ''),
    ],

    'africastalking' => [
        'username' => env('AFRICASTALKING_USERNAME', 'sandbox'),
        'api_key' => env('AFRICASTALKING_API_KEY'),
        'ussd_shortcode' => env('AFRICASTALKING_USSD_SHORTCODE', '*123#'),
    ],

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'passkey' => env('MPESA_PASSKEY'),
        'shortcode' => env('MPESA_SHORTCODE'),
    ],

    'llm' => [
    'mode'     => env('LLM_MODE', 'simulate'),     // 'simulate' or 'real'
    'endpoint' => env('LLM_ENDPOINT', ''),
    'api_key'  => env('LLM_API_KEY', ''),
],
];
