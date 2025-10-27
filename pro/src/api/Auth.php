<?php
// src/api/Auth.php
namespace App\Api;

use App\Api\Database;
use App\Api\JWT;
use Exception;

class Auth
{
    private Database $db;
    private JWT $jwt;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->jwt = new JWT();
    }

    public function login(string $email, string $password): array
    {
        try {
            // Find user by email
            $user = $this->db->fetch(
                "SELECT id, full_name, username, email, password, user_type, is_active, last_login 
                 FROM users 
                 WHERE email = ? AND is_active = 1",
                [$email]
            );

            if (!$user) {
                return [
                    'ok' => false,
                    'message' => 'ایمیل یا رمز عبور نادرست است',
                    'error_code' => 'INVALID_CREDENTIALS'
                ];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return [
                    'ok' => false,
                    'message' => 'ایمیل یا رمز عبور نادرست است',
                    'error_code' => 'INVALID_CREDENTIALS'
                ];
            }

            // Update last login
            $this->db->update(
                'users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = ?',
                [$user['id']]
            );

            // Generate tokens
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'user_type' => $user['user_type']
            ];

            $accessToken = $this->jwt->encode($payload);
            $refreshToken = $this->jwt->generateRefreshToken($payload);

            // Store refresh token
            $this->storeRefreshToken($user['id'], $refreshToken);

            // Log activity
            $this->logActivity($user['id'], 'login', 'کاربر وارد سیستم شد');

            return [
                'ok' => true,
                'token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type'],
                    'last_login' => $user['last_login']
                ],
                'expires_at' => date('Y-m-d H:i:s', time() + 86400) // 24 hours
            ];

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'ok' => false,
                'message' => 'خطا در ورود به سیستم',
                'error_code' => 'LOGIN_ERROR'
            ];
        }
    }

    public function signup(array $data): array
    {
        try {
            $this->db->beginTransaction();

            // Check if email already exists
            $existingUser = $this->db->fetch(
                "SELECT id FROM users WHERE email = ?",
                [$data['email']]
            );

            if ($existingUser) {
                return [
                    'ok' => false,
                    'message' => 'این ایمیل قبلاً ثبت شده است',
                    'error_code' => 'EMAIL_EXISTS'
                ];
            }

            // Check if username already exists
            $existingUsername = $this->db->fetch(
                "SELECT id FROM users WHERE username = ?",
                [$data['username']]
            );

            if ($existingUsername) {
                return [
                    'ok' => false,
                    'message' => 'این نام کاربری قبلاً انتخاب شده است',
                    'error_code' => 'USERNAME_EXISTS'
                ];
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert user
            $userId = $this->db->insert('users', [
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $hashedPassword,
                'birthdate' => $data['birthdate'],
                'gender' => $data['gender'],
                'building_address' => $data['building_address'],
                'user_type' => $data['user_type'],
                'floor' => $data['floor'],
                'unit' => $data['unit'],
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Generate tokens
            $payload = [
                'user_id' => $userId,
                'email' => $data['email'],
                'user_type' => $data['user_type']
            ];

            $accessToken = $this->jwt->encode($payload);
            $refreshToken = $this->jwt->generateRefreshToken($payload);

            // Store refresh token
            $this->storeRefreshToken($userId, $refreshToken);

            // Log activity
            $this->logActivity($userId, 'signup', 'کاربر جدید ثبت نام کرد');

            $this->db->commit();

            return [
                'ok' => true,
                'token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user' => [
                    'id' => $userId,
                    'full_name' => $data['full_name'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'user_type' => $data['user_type']
                ],
                'expires_at' => date('Y-m-d H:i:s', time() + 86400)
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Signup error: " . $e->getMessage());
            return [
                'ok' => false,
                'message' => 'خطا در ثبت نام',
                'error_code' => 'SIGNUP_ERROR'
            ];
        }
    }

    public function refreshToken(string $refreshToken): array
    {
        try {
            $payload = $this->jwt->decode($refreshToken);
            
            if ($payload['type'] !== 'refresh') {
                return [
                    'ok' => false,
                    'message' => 'نوع توکن نامعتبر است',
                    'error_code' => 'INVALID_TOKEN_TYPE'
                ];
            }

            // Verify refresh token exists in database
            $storedToken = $this->db->fetch(
                "SELECT user_id FROM refresh_tokens WHERE token = ? AND expires_at > NOW()",
                [$refreshToken]
            );

            if (!$storedToken) {
                return [
                    'ok' => false,
                    'message' => 'توکن نامعتبر است',
                    'error_code' => 'INVALID_REFRESH_TOKEN'
                ];
            }

            // Get user data
            $user = $this->db->fetch(
                "SELECT id, email, user_type FROM users WHERE id = ? AND is_active = 1",
                [$payload['user_id']]
            );

            if (!$user) {
                return [
                    'ok' => false,
                    'message' => 'کاربر یافت نشد',
                    'error_code' => 'USER_NOT_FOUND'
                ];
            }

            // Generate new tokens
            $newPayload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'user_type' => $user['user_type']
            ];

            $newAccessToken = $this->jwt->encode($newPayload);
            $newRefreshToken = $this->jwt->generateRefreshToken($newPayload);

            // Update refresh token
            $this->db->update(
                'refresh_tokens',
                ['token' => $newRefreshToken, 'expires_at' => date('Y-m-d H:i:s', time() + 604800)],
                'token = ?',
                [$refreshToken]
            );

            return [
                'ok' => true,
                'token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'expires_at' => date('Y-m-d H:i:s', time() + 86400)
            ];

        } catch (Exception $e) {
            error_log("Token refresh error: " . $e->getMessage());
            return [
                'ok' => false,
                'message' => 'خطا در تازه‌سازی توکن',
                'error_code' => 'REFRESH_ERROR'
            ];
        }
    }

    public function logout(string $token): array
    {
        try {
            $payload = $this->jwt->decode($token);
            
            // Remove refresh token
            $this->db->delete(
                'refresh_tokens',
                'user_id = ?',
                [$payload['user_id']]
            );

            // Log activity
            $this->logActivity($payload['user_id'], 'logout', 'کاربر از سیستم خارج شد');

            return [
                'ok' => true,
                'message' => 'با موفقیت خارج شدید'
            ];

        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return [
                'ok' => false,
                'message' => 'خطا در خروج از سیستم',
                'error_code' => 'LOGOUT_ERROR'
            ];
        }
    }

    public function getUserFromToken(string $token): ?array
    {
        try {
            $payload = $this->jwt->decode($token);
            
            return $this->db->fetch(
                "SELECT id, full_name, username, email, user_type, avatar, last_login 
                 FROM users 
                 WHERE id = ? AND is_active = 1",
                [$payload['user_id']]
            );

        } catch (Exception $e) {
            return null;
        }
    }

    private function storeRefreshToken(int $userId, string $token): void
    {
        $this->db->insert('refresh_tokens', [
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + 604800), // 7 days
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function logActivity(int $userId, string $type, string $description): void
    {
        $this->db->insert('activities', [
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
