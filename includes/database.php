<?php
require_once 'constants.php';

class Database {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle connection error, possibly redirect to setup or display a user-friendly message
            die("<h2>" . ERROR_DB_CONNECTION . "</h2><pre>" . $e->getMessage() . "</pre><br><a href='setup_tables.php'>Click here to run setup</a>");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function createDatabaseAndTables() {
        try {
            // Connect without specifying a database to create it
            $pdo = new PDO("mysql:host=" . DB_SERVER, DB_USERNAME, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Drop database if it exists
            $pdo->exec("DROP DATABASE IF EXISTS `" . DB_NAME . "`");

            // Create database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");

            // Reconnect to the newly created database
            $this->connect();

            // Create users table
            $sqlUsers = "CREATE TABLE IF NOT EXISTS `" . TABLE_USERS . "` (
                id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->conn->exec($sqlUsers);

            // Create accounts table
            $sqlAccounts = "CREATE TABLE IF NOT EXISTS `" . TABLE_ACCOUNTS . "` (
                id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                account_number VARCHAR(20) NOT NULL UNIQUE,
                account_type VARCHAR(50) NOT NULL,
                balance DECIMAL(10, 2) DEFAULT 0.00,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES `" . TABLE_USERS . "`(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->conn->exec($sqlAccounts);

            return true;
        } catch (PDOException $e) {
            error_log("Database setup error: " . $e->getMessage());
            return false;
        }
    }

    public function verifyTables() {
        $tables = [];
        try {
            $stmt = $this->conn->query("SHOW TABLES");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
        } catch (PDOException $e) {
            error_log("Error verifying tables: " . $e->getMessage());
        }
        return $tables;
    }

    public function getTableStructure($tableName) {
        $structure = [];
        try {
            $stmt = $this->conn->query("DESCRIBE `" . $tableName . "`");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $structure[] = $row;
            }
        } catch (PDOException $e) {
            error_log("Error getting table structure for " . $tableName . ": " . $e->getMessage());
        }
        return $structure;
    }
}

$database = new Database();
$conn = $database->getConnection();

?>