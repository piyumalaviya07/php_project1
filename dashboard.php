<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$success = $error = '';

// Handle account creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create") {
    $customer_name = trim($_POST["customer_name"]);
    $account_number = trim($_POST["account_number"]);
    $account_type = trim($_POST["account_type"]);
    $balance = trim($_POST["balance"]);
    $branch_code = trim($_POST["branch_code"]);
    $contact_number = trim($_POST["contact_number"]);
    
    if (empty($customer_name) || empty($account_number) || empty($account_type) || 
        empty($balance) || empty($branch_code) || empty($contact_number)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($account_number)) {
        $error = "Account number must be numeric.";
    } elseif (!is_numeric($balance)) {
        $error = "Balance must be numeric.";
    } elseif (strlen($contact_number) != 10 || !is_numeric($contact_number)) {
        $error = "Contact number must be 10 digits.";
    } else {
        $sql = "INSERT INTO accounts (customer_name, account_number, account_type, balance, branch_code, contact_number, user_id) 
                VALUES (:customer_name, :account_number, :account_type, :balance, :branch_code, :contact_number, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":customer_name", $customer_name);
        $stmt->bindParam(":account_number", $account_number);
        $stmt->bindParam(":account_type", $account_type);
        $stmt->bindParam(":balance", $balance);
        $stmt->bindParam(":branch_code", $branch_code);
        $stmt->bindParam(":contact_number", $contact_number);
        $stmt->bindParam(":user_id", $_SESSION["id"]);
        
        if ($stmt->execute()) {
            $success = "Account created successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

// Fetch all accounts
$sql = "SELECT * FROM accounts WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":user_id", $_SESSION["id"]);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Bank Management System</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Account</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="create">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" name="account_number" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Type</label>
                                <select class="form-control" name="account_type" required>
                                    <option value="Savings">Savings</option>
                                    <option value="Current">Current</option>
                                    <option value="Fixed Deposit">Fixed Deposit</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Balance</label>
                                <input type="number" step="0.01" class="form-control" name="balance" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Branch Code</label>
                                <input type="text" class="form-control" name="branch_code" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Account List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Account Number</th>
                                        <th>Type</th>
                                        <th>Balance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($accounts as $account): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($account["customer_name"]); ?></td>
                                        <td><?php echo htmlspecialchars($account["account_number"]); ?></td>
                                        <td><?php echo htmlspecialchars($account["account_type"]); ?></td>
                                        <td>₹<?php echo htmlspecialchars($account["balance"]); ?></td>
                                        <td>
                                            <a href="edit_account.php?id=<?php echo $account["id"]; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete_account.php?id=<?php echo $account["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>