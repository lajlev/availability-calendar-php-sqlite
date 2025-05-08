<?php
/**
 * API Router
 * 
 * This file handles all API requests and routes them to the appropriate handler.
 */

// Enable error reporting in development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Remove query string if present
$requestUri = strtok($requestUri, '?');

// Remove base path (assuming the API is at /api)
// For direct access to the PHP server, we don't need to remove a base path
if (strpos($requestUri, '/api') === 0) {
    $basePath = '/api';
    $requestPath = substr($requestUri, strlen($basePath));
} else {
    $requestPath = $requestUri;
}

// Remove leading and trailing slashes
$requestPath = trim($requestPath, '/');

// Split path into segments
$segments = explode('/', $requestPath);

// Get the first segment (endpoint)
$endpoint = isset($segments[0]) ? $segments[0] : '';

// Get the rest of the path
$path = isset($segments[1]) ? implode('/', array_slice($segments, 1)) : '';

// Route to the appropriate handler
switch ($endpoint) {
    case 'auth':
        // Authentication endpoints
        $_GET['path'] = $path;
        require_once __DIR__ . '/auth.php';
        break;
        
    case 'dates':
        // Date endpoints
        $_GET['path'] = $path;
        require_once __DIR__ . '/dates.php';
        break;
        
    default:
        // Invalid endpoint
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
        break;
}