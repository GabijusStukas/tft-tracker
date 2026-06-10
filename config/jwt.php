<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication
    |--------------------------------------------------------------------------
    |
    | Configure JWT authentication settings
    |
    */

    'secret' => env('JWT_SECRET'),
    'public' => env('JWT_PUBLIC_KEY'),
    'private' => env('JWT_PRIVATE_KEY'),
    'passphrase' => env('JWT_PASSPHRASE'),
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],
    'leeway' => env('JWT_LEEWAY', 0),
    'ttl' => env('JWT_TTL', 6000),
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160),
    'jwt_provider' => 'Tymon\JwtAuth\Providers\JWT\Namshi',
    'auth_provider' => 'Tymon\JwtAuth\Providers\Auth\Illuminate',
    'storage_provider' => 'Tymon\JwtAuth\Providers\Storage\Illuminate',
    'user_provider' => 'users',
    'hash_algorithm' => 'sha256',
];

