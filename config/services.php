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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
   'vnpay' => [
        'tmn_code' => env('VNP_TMNCODE'),
        'hash_secret' => env('VNP_HASHSECRET'),
        'url' => env('VNP_URL'),
        'return_url' => 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction',
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'ghn' => [
        'api_url' => env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn'),
        'token' => env('GHN_API_KEY'),
        'shop_id' => (int) env('GHN_SHOP_ID'),
        'from_district_id' => (int) env('GHN_SHOP_DISTRICT_ID', 1454),
        'from_ward_code' => env('GHN_SHOP_WARD_CODE', '21211'),
    ],

];
