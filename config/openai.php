<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | The OpenAI API key to use for API requests.
    |
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Organization
    |--------------------------------------------------------------------------
    |
    | The OpenAI organization to use for API requests.
    |
    */
    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests.
    |
    */
    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Enable Ticket Classification
    |--------------------------------------------------------------------------
    |
    | Whether to enable AI-powered ticket classification.
    |
    */
    'classify_enabled' => env('OPENAI_CLASSIFY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | SSL Certificate Path
    |--------------------------------------------------------------------------
    |
    | The path to the SSL certificate file for API requests.
    |
    */
    'ssl_cert_path' => env('OPENAI_SSL_CERT_PATH', 'D:/laragon/etc/ssl/cacert.pem'),
]; 