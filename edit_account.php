<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$error = $success = '';
$account = null;

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: dashboard.php");
    exit;
}

// Fetch account details
$sql = "SELECT * FROM accounts WHERE id = :id AND user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $_GET["id"]);
$stmt->bindParam(":user_id", $_SESSION["id"]);
$stmt->execute();

if ($stmt->rowCount() != 1) {
    header("location: dashboard.php");
    exit;
}

$account = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST["customer_name"]);
    $account_type = trim($_POST["account_type"]);
    $balance = trim($_POST["balance"]);
    $branch_code = trim($_POST["branch_code"]);
    $contact_number = trim($_POST["contact_number"]);
    
    if (empty($customer_name) || empty($account_type) || 
        empty($balance) || empty($branch_code) || empty($contact_number)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($balance)) {
        $error = "Balance must be numeric.";
    } elseif (strlen($contact_number) != 10 || !is_numeric($contact_number)) {
        $error = "Contact number must be 10 digits.";
    } else {
        $sql = "UPDATE accounts SET 
                customer_name = :customer_name,
                account_type = :account_type,
                balance = :balance,
                branch_code = :branch_code,
                contact_number = :contact_number
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":customer_name", $customer_name);
        $stmt->bindParam(":account_type", $account_type);
        $stmt->bindParam(":balance", $balance);
        $stmt->bindParam(":branch_code", $branch_code);
        $stmt->bindParam(":contact_number", $contact_number);
        $stmt->bindParam(":id", $_GET["id"]);
        $stmt->bindParam(":user_id", $_SESSION["id"]);
        
        if ($stmt->execute()) {
            $success = "Account updated successfully.";
            // Refresh account data
            $sql = "SELECT * FROM accounts WHERE id = :id AND user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $_GET["id"]);
            $stmt->bindParam(":user_id", $_SESSION["id"]);
            $stmt->execute();
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account - Bank Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Bank Management System</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Account</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if(!empty($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($account["account_number"]); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" value="<?php echo htmlspecialchars($account["customer_name"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Account Type</label>
                                <select class="form-control" name="account_type" required>
                                    <option value="Savings" <?php echo $account["account_type"] == "Savings" ? "selected" : ""; ?>>Savings</option>
                                    <option value="Current" <?php echo $account["account_type"] == "Current" ? "selected" : ""; ?>>Current</option>
                                    <option value="Fixed Deposit" <?php echo $account["account_type"] == "Fixed Deposit" ? "selected" : ""; ?>>Fixed Deposit</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Balance (₹)</label>
                                <input type="number" step="0.01" class="form-control" name="balance" value="<?php echo htmlspecialchars($account["balance"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Branch Code</label>
                                <input type="text" class="form-control" name="branch_code" value="<?php echo htmlspecialchars($account["branch_code"]); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($account["contact_number"]); ?>" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                                <button type="submit" class="btn btn-primary">Update Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>