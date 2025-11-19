<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bank_management";

    // Create connection without database first
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->exec($sql);
    echo "Database created successfully<br>";

    // Connect to the database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Users table created successfully<br>";

    // Create accounts table
    $sql = "CREATE TABLE IF NOT EXISTS accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        account_number VARCHAR(20) UNIQUE NOT NULL,
        account_type ENUM('Savings', 'Current', 'Fixed Deposit') NOT NULL,
        balance DECIMAL(10,2) NOT NULL,
        branch_code VARCHAR(10) NOT NULL,
        contact_number VARCHAR(10) NOT NULL,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Accounts table created successfully<br>";
    
    echo "<br>Setup completed successfully! <a href='register.php'>Click here to register</a>";

} catch(PDOException $e) {
    echo "<h2>Setup Error:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>