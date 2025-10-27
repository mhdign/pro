<?php
// config/app.php
return [
    'name' => $_ENV['APP_NAME'] ?? 'Financial Management System',
    'version' => '2.0.0',
    'environment' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'frontend_url' => $_ENV['FRONTEND_URL'] ?? 'http://localhost:3000',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Tehran',
    'locale' => $_ENV['APP_LOCALE'] ?? 'fa',
    'fallback_locale' => 'en',
    
    'providers' => [
        App\Providers\DatabaseServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\ValidationServiceProvider::class,
    ],
    
    'aliases' => [
        'DB' => App\Facades\Database::class,
        'Auth' => App\Facades\Auth::class,
        'Validator' => App\Facades\Validator::class,
    ],
    
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'your-secret-key',
        'expires_in' => $_ENV['JWT_EXPIRES_IN'] ?? '24h',
        'refresh_expires_in' => $_ENV['JWT_REFRESH_EXPIRES_IN'] ?? '7d',
        'algorithm' => 'HS256'
    ],
    
    'oauth' => [
        'google' => [
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
            'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? '/api/auth/oauth/google/callback'
        ],
        'microsoft' => [
            'client_id' => $_ENV['MICROSOFT_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['MICROSOFT_CLIENT_SECRET'] ?? '',
            'redirect_uri' => $_ENV['MICROSOFT_REDIRECT_URI'] ?? '/api/auth/oauth/microsoft/callback'
        ]
    ],
    
    'rate_limiting' => [
        'login' => [
            'max_attempts' => 10,
            'decay_minutes' => 1
        ],
        'signup' => [
            'max_attempts' => 5,
            'decay_minutes' => 1
        ],
        'api' => [
            'max_attempts' => 100,
            'decay_minutes' => 1
        ]
    ],
    
    'cors' => [
        'allowed_origins' => explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '*'),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'max_age' => 86400
    ]
];
