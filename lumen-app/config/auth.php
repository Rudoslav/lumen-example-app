<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD'),
    ],
    'guards' => [
        'TokenGuard' => [
            'driver' => 'token',
            'provider' => 'UserProvider'
        ],
    ],
    'providers' => [
        'UserProvider' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
    ]
];
