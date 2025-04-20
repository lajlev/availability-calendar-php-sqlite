<?php
/**
 * Date model for managing calendar dates
 */

require_once __DIR__ . '/../config/database.php';

class Date {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all dates with their statuses
     */
    public function getAllDates() {
        return $this->db->fetchAll('SELECT date, status FROM dates');
    }
    
    /**
     * Get a specific date by its date string
     */
    public function getDate($dateString) {
        return $this->db->fetchOne(
            'SELECT date, status FROM dates WHERE date = :date',
            [':date' => $dateString]
        );
    }
    
    /**
     * Update or create a date with a specific status
     */
    public function updateDate($dateString, $status) {
        // Check if the date exists
        $existingDate = $this->getDate($dateString);
        
        if ($existingDate) {
            // Update existing date
            $this->db->query(
                'UPDATE dates SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE date = :date',
                [':status' => $status, ':date' => $dateString]
            );
        } else {
            // Insert new date
            $this->db->insert(
                'INSERT INTO dates (date, status) VALUES (:date, :status)',
                [':date' => $dateString, ':status' => $status]
            );
        }
        
        return $this->getDate($dateString);
    }
}