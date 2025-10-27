<?php
// public/api.php
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

// Handle the API request
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
