<?php

return [
    'api_key'       => env('TRIPAY_API_KEY'),
    'private_key'   => env('TRIPAY_PRIVATE_KEY'),
    'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
    'api_url'       => env('TRIPAY_API_URL', 'https://tripay.co.id/api-sandbox'),
    'callback_url'  => env('TRIPAY_CALLBACK_URL'),
];
