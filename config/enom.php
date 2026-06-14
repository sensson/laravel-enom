<?php

return [
    'default' => env('ENOM_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'username' => env('ENOM_USERNAME'),
            'token' => env('ENOM_TOKEN'),
            'sandbox' => env('ENOM_SANDBOX', true),
        ],
    ],
];
