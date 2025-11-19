<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    // Prepare a delete statement
    $sql = "DELETE FROM accounts WHERE id = :id AND user_id = :user_id";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bindParam(":id", $_GET["id"]);
        $stmt->bindParam(":user_id", $_SESSION["id"]);
        
        if ($stmt->execute()) {
            $_SESSION["success_message"] = "Account deleted successfully.";
        } else {
            $_SESSION["error_message"] = "Something went wrong. Please try again later.";
        }
    }
}

header("location: dashboard.php");
exit;
?>