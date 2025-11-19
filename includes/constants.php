<?php
// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bank_management');

// Session timeout in seconds (e.g., 3600 seconds = 1 hour)
define('SESSION_TIMEOUT', 3600);

// Table names
define('TABLE_USERS', 'users');
define('TABLE_ACCOUNTS', 'accounts');

// Error messages
define('ERROR_DB_CONNECTION', 'Connection Error: Could not connect to the database. Please ensure MySQL is running and try again. If the issue persists, visit the setup page to initialize the database.');
define('ERROR_DB_SETUP_FAILED', 'Database setup failed. Please try again or contact support.');
define('ERROR_TABLE_CREATION_FAILED', 'Failed to create one or more tables.');
define('ERROR_USER_NOT_FOUND', 'User not found.');
define('ERROR_INVALID_PASSWORD', 'Invalid password.');
define('ERROR_ACCOUNT_NOT_FOUND', 'Account not found.');
define('ERROR_INSUFFICIENT_FUNDS', 'Insufficient funds.');
define('ERROR_TRANSACTION_FAILED', 'Transaction failed.');

// Success messages
define('SUCCESS_DB_SETUP', 'Database and tables created successfully!');
define('SUCCESS_PASSWORD_CHANGE', 'Password updated successfully.');
define('SUCCESS_ACCOUNT_CREATED', 'Account created successfully.');
define('SUCCESS_ACCOUNT_UPDATED', 'Account updated successfully.');
define('SUCCESS_ACCOUNT_DELETED', 'Account deleted successfully.');
define('SUCCESS_TRANSACTION', 'Transaction completed successfully.');

// Other constants
define('DEFAULT_BALANCE', 0.00);

?>