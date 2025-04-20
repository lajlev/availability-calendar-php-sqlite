<?php
/**
 * Development server script
 * 
 * This script starts both the PHP backend server and the Vite frontend server.
 * It's a simple way to run the application during development.
 */

// Configuration
$phpServerPort = 8000;
$phpServerRoot = __DIR__ . '/api';
$viteServerPort = 3000;

// Start PHP server
$phpServerCommand = sprintf(
    'php -S localhost:%d -t %s',
    $phpServerPort,
    escapeshellarg($phpServerRoot)
);

// Start Vite server
$viteServerCommand = 'npm run dev -- --port ' . $viteServerPort;

// Windows-specific adjustments
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Use start command to open new windows for each server
    $phpServerCommand = 'start cmd /k ' . $phpServerCommand;
    $viteServerCommand = 'start cmd /k ' . $viteServerCommand;
    
    // Run both commands
    system($phpServerCommand);
    system($viteServerCommand);
} else {
    // Unix-like systems (macOS, Linux)
    // Run both commands in the background
    $phpServerCommand .= ' > php-server.log 2>&1 &';
    $viteServerCommand .= ' > vite-server.log 2>&1 &';
    
    // Run both commands
    system($phpServerCommand);
    system($viteServerCommand);
    
    // Print instructions
    echo "Development servers started!\n";
    echo "PHP server running at http://localhost:$phpServerPort\n";
    echo "Vite server running at http://localhost:$viteServerPort\n";
    echo "Press Ctrl+C to stop the servers.\n";
    
    // Keep the script running to allow easy termination with Ctrl+C
    while (true) {
        sleep(1);
    }
}