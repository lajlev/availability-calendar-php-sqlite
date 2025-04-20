<?php
/**
 * Authentication API endpoints
 */

require_once __DIR__ . '/models/Session.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Create session model
$sessionModel = new Session();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request path
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Handle different endpoints
switch ($path) {
    case 'login':
        handleLogin($sessionModel);
        break;
    case 'logout':
        handleLogout($sessionModel);
        break;
    case 'check':
        handleCheck($sessionModel);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

/**
 * Handle login request
 */
function handleLogin($sessionModel) {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    // Get request body
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    
    // Debug log
    error_log('Login attempt - Raw input: ' . $rawInput);
    error_log('Login attempt - Decoded data: ' . print_r($data, true));
    
    // Check if password is provided
    if (!isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Password is required']);
        return;
    }
    
    // Debug log
    error_log('Password received: ' . $data['password']);
    error_log('Expected password: ' . $sessionModel->getAdminPassword());
    
    // Create session
    $token = $sessionModel->createSession($data['password']);
    
    if ($token) {
        // Set session cookie
        setcookie('admin_token', $token, time() + 86400, '/', '', false, true);
        
        // Return success
        echo json_encode([
            'authenticated' => true,
            'token' => $token
        ]);
    } else {
        // Return error
        http_response_code(401);
        echo json_encode([
            'authenticated' => false,
            'error' => 'Invalid password'
        ]);
    }
}

/**
 * Handle logout request
 */
function handleLogout($sessionModel) {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    // Get token from cookie
    $token = isset($_COOKIE['admin_token']) ? $_COOKIE['admin_token'] : null;
    
    if ($token) {
        // Delete session
        $sessionModel->deleteSession($token);
        
        // Clear cookie
        setcookie('admin_token', '', time() - 3600, '/', '', false, true);
    }
    
    // Return success
    echo json_encode(['success' => true]);
}

/**
 * Handle check request
 */
function handleCheck($sessionModel) {
    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    // Get token from cookie
    $token = isset($_COOKIE['admin_token']) ? $_COOKIE['admin_token'] : null;
    
    // Check if token is valid
    $authenticated = $token && $sessionModel->verifySession($token);
    
    // Return authentication status
    echo json_encode(['authenticated' => $authenticated]);
}