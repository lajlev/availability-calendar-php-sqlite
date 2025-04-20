<?php
/**
 * Database configuration and connection
 */

class Database {
    private $db;
    private static $instance = null;
    
    /**
     * Constructor - creates SQLite database connection
     */
    private function __construct() {
        $dbPath = __DIR__ . '/../../database/calendar.sqlite';
        $dbDir = dirname($dbPath);
        
        // Create database directory if it doesn't exist
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Create database connection
        $this->db = new SQLite3($dbPath);
        $this->db->enableExceptions(true);
        
        // Create tables if they don't exist
        $this->createTables();
    }
    
    /**
     * Get database instance (singleton pattern)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->db;
    }
    
    /**
     * Create database tables if they don't exist
     */
    private function createTables() {
        // Dates table
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS dates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date TEXT NOT NULL UNIQUE,
                status TEXT NOT NULL DEFAULT "available",
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
        
        // Admin sessions table
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS admin_sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_token TEXT NOT NULL UNIQUE,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }
    
    /**
     * Execute a query and return the result
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $result = $stmt->execute();
        return $result;
    }
    
    /**
     * Execute a query and return the last inserted ID
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->db->lastInsertRowID();
    }
    
    /**
     * Execute a query and return all rows as an array
     */
    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        $rows = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    /**
     * Execute a query and return a single row
     */
    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchArray(SQLITE3_ASSOC);
    }
}