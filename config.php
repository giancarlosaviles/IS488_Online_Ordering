<?php
// config.php
// Database configuration

// Hostname - from your SQL dump, it's studentdb-maria.gl.umbc.edu
define('DB_SERVER', 'studentdb-maria.gl.umbc.edu');

// Database Username - REPLACE WITH YOUR ACTUAL DATABASE USERNAME
define('DB_USERNAME', 'YOUR_DB_USERNAME_HERE');

// Database Password - REPLACE WITH YOUR ACTUAL DATABASE PASSWORD
define('DB_PASSWORD', 'YOUR_DB_PASSWORD_HERE');

// Database Name - from your SQL dump, it's mvijay1
define('DB_NAME', 'mvijay1');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    // If connection fails, stop script execution and show error.
    $connection_error = "ERROR: Could not connect to the database. Please check your configuration or contact support.";
    if (defined('DB_USERNAME') && DB_USERNAME === 'YOUR_DB_USERNAME_HERE') {
        $connection_error .= " (Hint: Make sure to replace placeholder credentials in config.php)";
    }
    // For development, you might still want the detailed mysqli_connect_error()
    // error_log("Database Connection Error: " . mysqli_connect_error()); // Log detailed error
    // Determine if placeholders are still in use to avoid showing detailed DB errors for that specific case.
    $show_detailed_error = true;
    if ( (defined('DB_USERNAME') && DB_USERNAME === 'YOUR_DB_USERNAME_HERE') || (defined('DB_PASSWORD') && DB_PASSWORD === 'YOUR_DB_PASSWORD_HERE') ){
        $show_detailed_error = false;
    }
    die($connection_error . ( $show_detailed_error ? " Details: " . mysqli_connect_error() : "" ) );
}

// Optional: Set character set to utf8mb4 for full Unicode support,
// though your tables are mostly latin1. It's good practice for new projects.
// mysqli_set_charset($link, "utf8mb4");

?>
