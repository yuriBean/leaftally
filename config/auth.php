<?php

return [

    'defaults' => [
            'guard' => 'web',
            'passwords' => 'users',
        ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'customer' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],
        'vender' => [
            'driver' => 'session',
            'provider' => 'venders',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

   'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\Models\User::class,
            ],
            'customers' => [
                'driver' => 'eloquent',
                'model' => App\Models\Customer::class,
            ],
            'venders' => [
                'driver' => 'eloquent',
                'model' => App\Models\Vender::class,
            ],

        ],

   'passwords' => [
            'users' => [
                'provider' => 'users',
                'table' => 'password_resets',
                'expire' => 60,
                'throttle' => 60,
            ],
        ],

    'password_timeout' => 10800,

];
