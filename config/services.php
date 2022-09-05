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

    'mailgun'   => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'legacy'    => [
        'host' => env('LEGACY_HOST'),
    ],

    'logistics' => [
        'url'      => env('LOGISTICS_API'),
        'username' => env('LOGISTICS_USERNAME'),
        'password' => env('LOGISTICS_PASSWORD'),
    ],

    'sap'       => [
        'url' => env('SAP_URL'),
    ],
    'sifco'     => [
        'url'           => env('SIFCO_URL'),
        'client_id'     => env('SIFCO_CLIENT_ID'),
        'client_secret' => env('SIFCO_CLIENT_SECRET'),
        'username'      => env('SIFCO_USERNAME'),
        'password'      => env('SIFCO_PASSWORD'),
    ],
];
