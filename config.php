<?php
require_once 'includes/constants.php';
require_once 'includes/database.php';

// Initialize the Database class and get the connection
$database = new Database();
$conn = $database->getConnection();

// Start session only if one isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) {
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header("location: login.php?timeout=true");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time

?>