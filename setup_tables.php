<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Database Setup Status</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "bank_management";

                    // Test MySQL connection
                    $conn = @new PDO("mysql:host=$servername", $username, $password);
                    if (!$conn) {
                        throw new PDOException("Could not connect to MySQL server. Please ensure MySQL is running.");
                    }
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo "<div class='alert alert-success'>MySQL connection successful</div>";

                    // Drop database if exists and create new
                    $sql = "DROP DATABASE IF EXISTS $dbname";
                    $conn->exec($sql);
                    $sql = "CREATE DATABASE $dbname";
                    $conn->exec($sql);
                    echo "<div class='alert alert-success'>Database 'bank_management' created successfully</div>";

                    // Connect to the new database
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Create users table
                    $sql = "CREATE TABLE users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB";
                    $conn->exec($sql);
                    echo "<div class='alert alert-success'>Users table created successfully</div>";

                    // Create accounts table
                    $sql = "CREATE TABLE accounts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        customer_name VARCHAR(100) NOT NULL,
                        account_number VARCHAR(20) UNIQUE NOT NULL,
                        account_type ENUM('Savings', 'Current', 'Fixed Deposit') NOT NULL,
                        balance DECIMAL(10,2) NOT NULL,
                        branch_code VARCHAR(10) NOT NULL,
                        contact_number VARCHAR(10) NOT NULL,
                        user_id INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB";
                    $conn->exec($sql);
                    echo "<div class='alert alert-success'>Accounts table created successfully</div>";

                    // Verify tables
                    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    if (count($tables) > 0) {
                        echo "<div class='alert alert-info'><strong>Available tables:</strong><br>";
                        foreach ($tables as $table) {
                            echo "- $table<br>";
                            // Show table structure
                            $structure = $conn->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
                            echo "<pre class='ms-3'>";
                            print_r($structure);
                            echo "</pre>";
                        }
                        echo "</div>";
                        echo "<div class='alert alert-success'><strong>Setup completed successfully!</strong><br>
                              <a href='register.php' class='btn btn-primary mt-3'>Proceed to Registration</a></div>";
                    } else {
                        throw new PDOException("No tables were created. Please check MySQL permissions.");
                    }

                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'><strong>Setup Error:</strong><br>" . $e->getMessage() . "</div>";
                    echo "<div class='alert alert-info'>Troubleshooting steps:<br>
                          1. Ensure MySQL service is running in XAMPP<br>
                          2. Verify root user has no password<br>
                          3. Check if port 3306 is available<br>
                          4. Ensure you have proper permissions</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>