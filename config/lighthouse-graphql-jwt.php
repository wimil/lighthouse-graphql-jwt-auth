<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GraphQL schema
    |--------------------------------------------------------------------------
    |
    | File path of the GraphQL schema to be used, defaults to null so it uses
    | the default location
    |
    */
    'schema' => null,
    /*
    |--------------------------------------------------------------------------
    | Settings for email verification
    |--------------------------------------------------------------------------
    |
    | Update this values for your use case
    |
    */
    'verify_email' => [
        'base_url' => env('APP_URL').'/email-verify',
    ],
];