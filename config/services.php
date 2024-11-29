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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sms_gateway' => [
        'url' => env('SMS_GATEWAY_URL', 'https://unify.smsgateway.center/SMSApi/send'),
        'sender_id' => env('SMS_SENDER_ID', 'wmtour'),
        'account_name' => env('SMS_ACCOUNT_NAME', 'wishmytour'),
        'account_password' => env('SMS_ACCOUNT_PASSWORD', 'Web@2023##'),
        'request_timeout' => env('SMS_REQUEST_TIMEOUT', 30),
        'dlt_entity_id' => env('SMS_ENTITY_ID', 1701171862888821963),
    ],

];
