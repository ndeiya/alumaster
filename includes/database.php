<?php
/**
 * AluMaster Aluminum System - Database Connection Class
 * Handles PDO database connections with error handling and security
 */

// Database connection class for AluMaster Aluminum System

class Database {
    private $connection;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        $this->host = DB_HOST ?? 'localhost';
        $this->dbname = DB_NAME ?? 'alumaster';
        $this->username = DB_USER ?? 'root';
        $this->password = DB_PASS ?? '';
    }

    public function getConnection() {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // For now, just return null if database connection fails
                // This allows the site to load even without database
                error_log("Database connection failed: " . $e->getMessage());
                return null;
            }
        }
        
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $connection = $this->getConnection();
            if (!$connection) {
                return false;
            }
            
            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query failed: " . $e->getMessage());
            return false;
        }
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt !== false;
    }

    public function lastInsertId() {
        $connection = $this->getConnection();
        return $connection ? $connection->lastInsertId() : false;
    }
}
?>