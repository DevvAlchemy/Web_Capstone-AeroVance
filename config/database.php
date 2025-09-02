<?php

define('BASE_PATH', dirname(__DIR__)); // This gets the helicopter-marketplace directory

 // Handles database connection and PDO setup
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $connection;
    
    public function __construct() {
        // Load from environment variables or use defaults
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'helicopter_marketplace';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }
    
    /**
     * Create database connection
     * @return PDO|null
     */
    public function connect() {
        $this->connection = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
        
        return $this->connection;
    }
    
    /**
     * Get current connection
     * @return PDO|null
     */
    public function getConnection() {
        if ($this->connection === null) {
            return $this->connect();
        }
        return $this->connection;
    }
    
     // Close database connection
    public function disconnect() {
        $this->connection = null;
    }
    
    // Begin transaction 
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        return $this->getConnection()->rollback();
    }
}

// Create global database instance
$database = new Database();
$pdo = $database->connect();
?>