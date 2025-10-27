<?php
// src/api/routes.php
namespace App\Api;

use App\Api\Auth;
use App\Api\Database;

class ApiRoutes
{
    private Auth $auth;
    private Database $db;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->db = Database::getInstance();
        
        // Set CORS headers
        $this->setCorsHeaders();
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/api', '', $path);

        // Handle preflight requests
        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        try {
            switch ($path) {
                case '/auth/login':
                    if ($method === 'POST') {
                        $this->handleLogin();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                case '/auth/signup':
                    if ($method === 'POST') {
                        $this->handleSignup();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                case '/auth/refresh':
                    if ($method === 'POST') {
                        $this->handleRefreshToken();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                case '/auth/logout':
                    if ($method === 'POST') {
                        $this->handleLogout();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                case '/auth/me':
                    if ($method === 'GET') {
                        $this->handleGetUser();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                case '/dashboard/stats':
                    if ($method === 'GET') {
                        $this->handleGetStats();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;

                default:
                    $this->sendError('Route not found', 404);
                    break;
            }
        } catch (Exception $e) {
            error_log("API Error: " . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    private function handleLogin(): void
    {
        $data = $this->getJsonInput();
        
        if (!isset($data['email']) || !isset($data['password'])) {
            $this->sendError('ایمیل و رمز عبور الزامی است', 400);
            return;
        }

        $result = $this->auth->login($data['email'], $data['password']);
        $this->sendResponse($result, $result['ok'] ? 200 : 401);
    }

    private function handleSignup(): void
    {
        $data = $this->getJsonInput();
        
        $requiredFields = [
            'full_name', 'email', 'phone', 'username', 'password', 
            'confirm_password', 'birthdate', 'gender', 'building_address', 
            'user_type', 'floor', 'unit'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendError("فیلد {$field} الزامی است", 400);
                return;
            }
        }

        if ($data['password'] !== $data['confirm_password']) {
            $this->sendError('رمز عبور و تکرار آن مطابقت ندارند', 400);
            return;
        }

        if (!isset($data['terms']) || !$data['terms']) {
            $this->sendError('باید با قوانین و مقررات موافقت کنید', 400);
            return;
        }

        $result = $this->auth->signup($data);
        $this->sendResponse($result, $result['ok'] ? 201 : 400);
    }

    private function handleRefreshToken(): void
    {
        $data = $this->getJsonInput();
        
        if (!isset($data['refresh_token'])) {
            $this->sendError('توکن تازه‌سازی الزامی است', 400);
            return;
        }

        $result = $this->auth->refreshToken($data['refresh_token']);
        $this->sendResponse($result, $result['ok'] ? 200 : 401);
    }

    private function handleLogout(): void
    {
        $token = $this->getBearerToken();
        
        if (!$token) {
            $this->sendError('توکن احراز هویت الزامی است', 401);
            return;
        }

        $result = $this->auth->logout($token);
        $this->sendResponse($result, $result['ok'] ? 200 : 401);
    }

    private function handleGetUser(): void
    {
        $token = $this->getBearerToken();
        
        if (!$token) {
            $this->sendError('توکن احراز هویت الزامی است', 401);
            return;
        }

        $user = $this->auth->getUserFromToken($token);
        
        if (!$user) {
            $this->sendError('کاربر یافت نشد', 404);
            return;
        }

        $this->sendResponse([
            'ok' => true,
            'user' => $user
        ]);
    }

    private function handleGetStats(): void
    {
        $token = $this->getBearerToken();
        
        if (!$token) {
            $this->sendError('توکن احراز هویت الزامی است', 401);
            return;
        }

        $user = $this->auth->getUserFromToken($token);
        
        if (!$user) {
            $this->sendError('کاربر یافت نشد', 404);
            return;
        }

        $userId = $user['id'];

        // Get transaction count
        $transactionCount = $this->db->fetch(
            "SELECT COUNT(*) as count FROM transactions WHERE user_id = ?",
            [$userId]
        )['count'] ?? 0;

        // Get unread messages
        $unreadMessages = $this->db->fetch(
            "SELECT COUNT(*) as count FROM messages WHERE recipient_id = ? AND is_read = 0",
            [$userId]
        )['count'] ?? 0;

        // Get pending tasks
        $pendingTasks = $this->db->fetch(
            "SELECT COUNT(*) as count FROM tasks WHERE assigned_to = ? AND status = 'pending'",
            [$userId]
        )['count'] ?? 0;

        // Get financial stats
        $income = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income' AND status = 'completed'",
            [$userId]
        )['total'] ?? 0;

        $expense = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense' AND status = 'completed'",
            [$userId]
        )['total'] ?? 0;

        $this->sendResponse([
            'ok' => true,
            'stats' => [
                'transaction_count' => (int) $transactionCount,
                'unread_messages' => (int) $unreadMessages,
                'pending_tasks' => (int) $pendingTasks,
                'total_income' => (float) $income,
                'total_expense' => (float) $expense,
                'balance' => (float) $income - (float) $expense
            ]
        ]);
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }
        
        return $data ?: [];
    }

    private function getBearerToken(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    private function setCorsHeaders(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $cors = $config['cors'];
        
        header('Access-Control-Allow-Origin: ' . implode(', ', $cors['allowed_origins']));
        header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers']));
        header('Access-Control-Max-Age: ' . $cors['max_age']);
    }

    private function sendResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function sendError(string $message, int $statusCode = 400): void
    {
        $this->sendResponse([
            'ok' => false,
            'message' => $message,
            'error_code' => $this->getErrorCode($statusCode)
        ], $statusCode);
    }

    private function getErrorCode(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            500 => 'INTERNAL_SERVER_ERROR',
            default => 'UNKNOWN_ERROR'
        };
    }
}
