<?php
/**
 * Dates API endpoints
 */

require_once __DIR__ . '/models/Date.php';
require_once __DIR__ . '/models/Session.php';

// Set headers
header('Content-Type: application/json');

// Create models
$dateModel = new Date();
$sessionModel = new Session();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request path
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Check if path contains a date (e.g., "2023-04-15")
$datePattern = '/^\d{4}-\d{2}-\d{2}$/';
$isDatePath = preg_match($datePattern, $path);

// Handle different endpoints
if ($path === '' || $path === '/') {
    // Get all dates
    handleGetAllDates($dateModel);
} elseif ($isDatePath) {
    // Handle specific date
    handleDateRequest($dateModel, $sessionModel, $path);
} else {
    // Invalid endpoint
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}

/**
 * Handle request to get all dates
 */
function handleGetAllDates($dateModel) {
    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    // Get all dates
    $dates = $dateModel->getAllDates();
    
    // Return dates
    echo json_encode($dates);
}

/**
 * Handle request for a specific date
 */
function handleDateRequest($dateModel, $sessionModel, $dateString) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get date
            $date = $dateModel->getDate($dateString);
            
            if ($date) {
                echo json_encode($date);
            } else {
                echo json_encode([
                    'date' => $dateString,
                    'status' => 'available'
                ]);
            }
            break;
            
        case 'POST':
            // Update date status (admin only)
            handleUpdateDate($dateModel, $sessionModel, $dateString);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

/**
 * Handle request to update a date's status
 */
function handleUpdateDate($dateModel, $sessionModel, $dateString) {
    // Check if user is authenticated
    $token = isset($_COOKIE['admin_token']) ? $_COOKIE['admin_token'] : null;
    
    if (!$token || !$sessionModel->verifySession($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }
    
    // Get request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if status is provided
    if (!isset($data['status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Status is required']);
        return;
    }
    
    // Validate status
    $validStatuses = ['available', 'booked', 'half-booked'];
    if (!in_array($data['status'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status']);
        return;
    }
    
    // Update date
    $date = $dateModel->updateDate($dateString, $data['status']);
    
    // Return updated date
    echo json_encode($date);
}