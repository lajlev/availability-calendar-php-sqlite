<?php
/**
 * Session model for managing admin sessions
 */

require_once __DIR__ . '/../config/database.php';

class Session {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initializeAdminPassword();
    }
    
    /**
     * Initialize admin password if it doesn't exist
     */
    private function initializeAdminPassword() {
        // Check if admin_settings table exists, if not create it
        $this->db->query('
            CREATE TABLE IF NOT EXISTS admin_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
        
        // Check if admin password exists
        $password = $this->getAdminPassword();
        
    }
    
    /**
     * Create a new admin session
     */
    public function createSession($password) {
        // Verify password
        $adminPassword = $this->getAdminPassword();
        if ($password !== $adminPassword) {
            return false;
        }
        
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        
        // Set expiration time (24 hours from now)
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);
        
        // Insert session into database
        $this->db->insert(
            'INSERT INTO admin_sessions (session_token, expires_at) VALUES (:token, :expires_at)',
            [':token' => $token, ':expires_at' => $expiresAt]
        );
        
        return $token;
    }
    
    /**
     * Verify if a session token is valid
     */
    public function verifySession($token) {
        // Clean up expired sessions
        $this->cleanupExpiredSessions();
        
        // Check if token exists and is not expired
        $session = $this->db->fetchOne(
            'SELECT * FROM admin_sessions WHERE session_token = :token AND expires_at > CURRENT_TIMESTAMP',
            [':token' => $token]
        );
        
        return $session !== false;
    }
    
    /**
     * Delete a session (logout)
     */
    public function deleteSession($token) {
        $this->db->query(
            'DELETE FROM admin_sessions WHERE session_token = :token',
            [':token' => $token]
        );
        
        return true;
    }
    
    /**
     * Clean up expired sessions
     */
    private function cleanupExpiredSessions() {
        $this->db->query('DELETE FROM admin_sessions WHERE expires_at < CURRENT_TIMESTAMP');
    }
    
    /**
     * Get admin password from database
     */
    public function getAdminPassword() {
        $result = $this->db->fetchOne(
            'SELECT setting_value FROM admin_settings WHERE setting_key = :key',
            [':key' => 'admin_password']
        );
        
        return $result ? $result['setting_value'] : null;
    }
    
    /**
     * Set or update admin password in database
     */
    public function setAdminPassword($password) {
        // Check if password already exists
        $existingPassword = $this->getAdminPassword();
        
        if ($existingPassword) {
            // Update existing password
            $this->db->query(
                'UPDATE admin_settings SET setting_value = :value, updated_at = CURRENT_TIMESTAMP WHERE setting_key = :key',
                [':key' => 'admin_password', ':value' => $password]
            );
        } else {
            // Insert new password
            $this->db->insert(
                'INSERT INTO admin_settings (setting_key, setting_value) VALUES (:key, :value)',
                [':key' => 'admin_password', ':value' => $password]
            );
        }
        
        return true;
    }
}