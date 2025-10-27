<?php
/**
 * Simple Authentication API
 * This file handles login and signup endpoints
 */

header('Content-Type: application/json; charset=utf-8');

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database connection
require_once __DIR__ . '/../includes/db.php';

// Get database connection
global $conn;

// Get action from query string
$action = $_GET['action'] ?? '';

// Route to appropriate handler
switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    case 'signup':
        handleSignup($conn);
        break;
    default:
        http_response_code(404);
        echo json_encode([
            'ok' => false,
            'message' => 'Route not found',
            'error_code' => 'NOT_FOUND'
        ]);
        break;
}

function handleLogin($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'ایمیل و رمز عبور الزامی است',
            'error_code' => 'MISSING_FIELDS'
        ]);
        return;
    }
    
    $email = $data['email'];
    $password = $data['password'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'message' => 'ایمیل یا رمز عبور نادرست است',
            'error_code' => 'INVALID_CREDENTIALS'
        ]);
        return;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'message' => 'ایمیل یا رمز عبور نادرست است',
            'error_code' => 'INVALID_CREDENTIALS'
        ]);
        return;
    }
    
    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    
    // Generate simple token (in production, use JWT)
    $token = base64_encode(json_encode([
        'user_id' => $user['id'],
        'email' => $user['email'],
        'exp' => time() + 86400
    ]));
    
    // Return success
    echo json_encode([
        'ok' => true,
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'user_type' => $user['user_type']
        ]
    ]);
}

function handleSignup($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
        return;
    }
    
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Validate required fields
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['birthdate'])) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'فیلدهای الزامی خالی است',
            'error_code' => 'MISSING_FIELDS'
        ]);
        return;
    }
    
    $name = $data['name'];
    $email = $data['email'];
    $password = $data['password'];
    $birthdate = $data['birthdate'];
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'این ایمیل قبلاً ثبت شده است',
            'error_code' => 'EMAIL_EXISTS'
        ]);
        return;
    }
    
    // Generate username from email (you can improve this)
    $username = explode('@', $email)[0];
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users (full_name, username, email, password, birthdate, user_type, is_active, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, 'tenant', 1, NOW(), NOW())
    ");
    $stmt->bind_param("sssss", $name, $username, $email, $hashedPassword, $birthdate);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        
        // Generate token
        $token = base64_encode(json_encode([
            'user_id' => $userId,
            'email' => $email,
            'exp' => time() + 86400
        ]));
        
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'token' => $token,
            'user' => [
                'id' => $userId,
                'full_name' => $name,
                'email' => $email,
                'user_type' => 'tenant'
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'message' => 'خطا در ثبت نام',
            'error_code' => 'SIGNUP_ERROR'
        ]);
    }
}
?>