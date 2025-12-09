<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | এই option দিয়ে আপনার default authentication "guard" এবং password
    | reset "broker" define করা হয়। Laravel 12 + Sanctum এ আমরা সাধারণত
    | web guard session এর জন্য এবং API routes এ Sanctum ব্যবহার করি।
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guard define করে দেয় কোন authentication driver use হবে। Laravel 12 + Sanctum
    | এর ক্ষেত্রে API guard ব্যবহার করা হলে Sanctum middleware handle করবে।
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Providers define করে users কে database থেকে কিভাবে fetch করা হবে।
    | Laravel 12 + Sanctum এ আমরা সাধারণত Eloquent ব্যবহার করি।
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // যদি database driver ব্যবহার করতে চান:
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Password reset configuration। expire time মিনিটে, throttle seconds এ define।
    | Token table এর নাম environment variable থেকে নেওয়া যায়।
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,   // Token validity: 60 মিনিট
            'throttle' => 60, // 60 seconds wait before generating new token
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Password confirmation timeout (seconds)। Default 3 hours।
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
