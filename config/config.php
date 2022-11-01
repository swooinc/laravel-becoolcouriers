<?php

return [
    'api' => [
        'key' => env('BECOOL_API_KEY'),
    ],

    'cache' => [
        'zones' => env('BECOOL_ZONES_CACHE'),
    ],

    'sandbox' => env('BECOOL_SANDBOX', false),
];
