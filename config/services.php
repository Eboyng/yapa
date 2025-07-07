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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Paystack Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Paystack payment gateway integration.
    |
    */
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Kudisms Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Kudisms WhatsApp/SMS API integration.
    |
    */
    'kudisms' => [
        'api_key' => env('KUDISMS_API_KEY'),
        'sender_id' => env('KUDISMS_SENDER_ID', 'Yapa'),
        'base_url' => env('KUDISMS_BASE_URL', 'https://api.kudisms.net'),
        'whatsapp_url' => env('KUDISMS_WHATSAPP_URL', 'https://my.kudisms.net/api/whatsapp'),
        'whatsapp_template_code' => env('KUDISMS_WHATSAPP_TEMPLATE_CODE'),
        'whatsapp_enabled' => env('KUDISMS_WHATSAPP_ENABLED', true),
        'sms_enabled' => env('KUDISMS_SMS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google APIs (People API for contact filtering).
    |
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'people_api_enabled' => env('GOOGLE_PEOPLE_API_ENABLED', false),
    ],

];
