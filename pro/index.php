<?php
/**
 * Main Entry Point for Pro Application
 * Handles routing and serves React frontend
 */

// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Tehran');

// Set charset
header('Content-Type: text/html; charset=UTF-8');

// Database connection
require_once __DIR__ . '/includes/db.php';

// Get the current path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove leading slash and query string
$path = ltrim(strtok($path, '?'), '/');

// Handle different routes
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم مدیریت مالی - Pro</title>
    
    <!-- Include CSS -->
    <link rel="stylesheet" href="/css/globals.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            direction: rtl;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Tailwind-like utility classes */
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .bg-gradient-to-br { background: linear-gradient(to bottom right, #f8fafc, #dbeafe, #eef2ff); }
        .from-slate-50 { background-color: #f8fafc; }
        .via-blue-50 { background-color: #eff6ff; }
        .to-indigo-100 { background-color: #e0e7ff; }
        .p-8 { padding: 2rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-bold { font-weight: 700; }
        .text-slate-800 { color: #1e293b; }
        .text-slate-600 { color: #475569; }
        .bg-white { background-color: #ffffff; }
        .rounded-3xl { border-radius: 1.5rem; }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .border { border-width: 1px; border-style: solid; }
        .border-white { border-color: rgba(255, 255, 255, 0.2); }
        
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(to right, #6366f1, #9333ea);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }
        
        .grid { display: grid; gap: 1.5rem; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-success { background: #dcfce7; color: #166534; }
        .status-error { background: #fee2e2; color: #991b1b; }
        .status-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br p-8">
        <div class="container">
            <div class="card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h1 class="text-3xl font-bold text-slate-800 mb-2">
                        🚀 سیستم مدیریت مالی Pro
                    </h1>
                    <p class="text-slate-600">پروژه PHP + React با XAMPP</p>
                </div>
                
                <?php
                // Check database connection
                global $database, $conn;
                if (isset($database)) {
                    if ($database->getError()) {
                        echo '<div class="status-badge status-error" style="margin-bottom: 2rem;">';
                        echo '❌ خطا در اتصال به پایگاه داده: ' . $database->getError();
                        echo '</div>';
                        echo '<p><a href="setup_database.php" class="btn">راه‌اندازی پایگاه داده</a></p>';
                    } else {
                        echo '<div class="status-badge status-success" style="margin-bottom: 2rem;">';
                        echo '✓ اتصال به پایگاه داده برقرار شد';
                        echo '</div>';
                        
                        // Check if database is empty
                        $result = $conn->query("SHOW TABLES");
                        $tableCount = $result->num_rows;
                        
                        if ($tableCount == 0) {
                            echo '<div class="status-badge status-warning" style="margin-bottom: 2rem;">';
                            echo '⚠️ جدول‌های پایگاه داده ایجاد نشده‌اند';
                            echo '</div>';
                            echo '<p><a href="setup_database.php" class="btn">ایجاد جداول پایگاه داده</a></p>';
                        } else {
                            echo '<div class="status-badge status-success" style="margin-bottom: 2rem;">';
                            echo "✓ تعداد جدول‌های پایگاه داده: $tableCount";
                            echo '</div>';
                        }
                    }
                }
                ?>
                
                <div style="margin-top: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1e293b;">
                        صفحات برنامه:
                    </h2>
                    
                    <div class="grid grid-cols-2">
                        <div class="card" style="text-align: center;">
                            <h3 style="margin-bottom: 1rem; color: #6366f1;">🔐 ورود</h3>
                            <p style="margin-bottom: 1rem; color: #64748b;">صفحه ورود کاربران</p>
                            <a href="login.html" class="btn">رفتن به صفحه ورود</a>
                        </div>
                        
                        <div class="card" style="text-align: center;">
                            <h3 style="margin-bottom: 1rem; color: #9333ea;">📝 ثبت‌نام</h3>
                            <p style="margin-bottom: 1rem; color: #64748b;">ایجاد حساب کاربری جدید</p>
                            <a href="signup.html" class="btn">رفتن به صفحه ثبت‌نام</a>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
                    <h3 style="margin-bottom: 1rem; font-weight: 600; color: #1e293b;">پیوندهای مفید:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="setup_database.php" class="btn" style="display: inline-block; background: #64748b; padding: 8px 16px; font-size: 14px;">راه‌اندازی پایگاه داده</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="public/index.php" class="btn" style="display: inline-block; background: #10b981; padding: 8px 16px; font-size: 14px;">Public API</a></li>
                        <li><a href="phpmyadmin" class="btn" style="display: inline-block; background: #6366f1; padding: 8px 16px; font-size: 14px;">phpMyAdmin</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
