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

    'google' => [
        'client_id' => '776592341454-v02v8io816mejiuuhrm0gtr0i0iea7m2.apps.googleusercontent.com',
        'client_secret' => 'NQNYjK3uHChUl8lhK8lbwKGs',
        'redirect' => 'http://localhost:8000/callback/google',
    ], 

    'linkedin' => [
        'redirect_uri' => 'http://localhost:3000/linkedin',
        'client_id' => '86b3ingqrtcw1l',
        'client_secret' => 'tOjZIMNF6jiA836l',
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'secret' => env('STRIPE_PUBLISHING_KEY'),
    ],
        
    'stripe_secret' => env('STRIPE_PUBLISHING_KEY'),
];
