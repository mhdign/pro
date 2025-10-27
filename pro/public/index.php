<?php
// public/index.php
require_once __DIR__ . '/../src/api/routes.php';

use App\Api\ApiRoutes;

// Set error reporting based on environment
$config = require __DIR__ . '/../config/app.php';
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set($config['timezone']);

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Handle API requests
if (strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
    try {
        $api = new ApiRoutes();
        $api->handleRequest();
    } catch (Exception $e) {
        error_log("API Error: " . $e->getMessage());
        
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => false,
            'message' => 'خطای داخلی سرور',
            'error_code' => 'INTERNAL_SERVER_ERROR'
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// Handle frontend requests
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove query string
$path = strtok($path, '?');

// Route to appropriate page
switch ($path) {
    case '/':
    case '/index.php':
        include __DIR__ . '/../src/pages/Home.php';
        break;
        
    case '/login':
        include __DIR__ . '/../src/pages/Login.php';
        break;
        
    case '/signup':
        include __DIR__ . '/../src/pages/Signup.php';
        break;
        
    case '/home':
    case '/dashboard':
        include __DIR__ . '/../src/pages/Dashboard.php';
        break;
        
    default:
        // Check if it's a static file
        $filePath = __DIR__ . $path;
        if (file_exists($filePath) && is_file($filePath)) {
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            readfile($filePath);
        } else {
            // 404 Not Found
            http_response_code(404);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحه یافت نشد - سیستم مدیریت مالی</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-slate-300 dark:text-slate-600 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-slate-700 dark:text-slate-300 mb-4">صفحه یافت نشد</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-8">صفحه‌ای که دنبال آن هستید وجود ندارد.</p>
            <a href="/" class="btn btn--primary">بازگشت به صفحه اصلی</a>
        </div>
    </div>
</body>
</html>';
        }
        break;
}
