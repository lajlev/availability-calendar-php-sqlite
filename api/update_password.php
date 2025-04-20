<?php
/**
 * Script to update the admin password
 */

require_once __DIR__ . '/models/Session.php';

// Create session model
$sessionModel = new Session();

// Update the password to "morjet7913"
$newPassword = 'morjet7913';
$result = $sessionModel->setAdminPassword($newPassword);

// Output result
if ($result) {
    echo "Password successfully updated to: " . $newPassword . "\n";
    echo "You can now log in with this new password.\n";
} else {
    echo "Failed to update password.\n";
}