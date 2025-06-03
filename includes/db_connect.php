<?php
// includes/db_connect.php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Assuming empty password for local dev
define('DB_NAME', 'archiaxis_db');

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Do not output detailed errors in production
    // error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Optional: Set character set to utf8mb4 for better Unicode support
if (!$conn->set_charset("utf8mb4")) {
    // error_log("Error loading character set utf8mb4: " . $conn->error);
    // In production, you might not want to die here, but log the error.
    // For development, it's fine to see the error.
    // die("Error loading character set utf8mb4.");
}

// The $conn variable is now available for use in other scripts that include this file.
?>
