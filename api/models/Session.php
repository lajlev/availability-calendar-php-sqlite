<?php
/**
 * Session model for managing admin sessions
 */

require_once __DIR__ . '/../config/database.php';

class Session {
    private $db;
    private $adminPassword = 'morjet'; // In a real app, this would be stored securely
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new admin session
     */
    public function createSession($password) {
        // Verify password
        if ($password !== $this->adminPassword) {
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
     * Get admin password (for debugging only)
     */
    public function getAdminPassword() {
        return $this->adminPassword;
    }
}