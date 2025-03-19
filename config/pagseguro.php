<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PagSeguro Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials and configuration for PagSeguro
    | payment gateway integration.
    |
    */

    // Ambiente de sandbox (true) ou produção (false)
    'sandbox' => env('PAGSEGURO_SANDBOX', true),

    // Token de acesso à API do PagSeguro
    'token' => env('PAGSEGURO_TOKEN', ''),

    // Email da conta PagSeguro
    'email' => env('PAGSEGURO_EMAIL', ''),

    // Chave pública para criptografia do cartão (checkout transparente)
    'public_key' => env('PAGSEGURO_PUBLIC_KEY', ''),
];
