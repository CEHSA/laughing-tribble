<?php
// Database Configuration

define('DB_HOST', '127.0.0.1'); // Or 'localhost'
define('DB_PORT', '3306');     // Default MySQL port
define('DB_DATABASE', 'architex_axis_db');
define('DB_USERNAME', 'root'); // Common default, consider creating a dedicated user
define('DB_PASSWORD', '');     // Common default for root with no password, adjust if needed

/**
 * Establishes a PDO database connection.
 *
 * @return PDO|null Returns a PDO connection object on success, or null on failure.
 *                  In a real application, more robust error handling (e.g., logging) is advised.
 */
function get_db_connection() {
    // DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
    // Note: DB_DATABASE is initially excluded from DSN for creating the database itself.
    // It will be included in DSN when connecting to the specific database later.

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Important: throw exceptions on error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        // In a real application, log this error instead of echoing
        // For this script, we might echo or let the caller handle it.
        error_log("Database connection failed: " . $e->getMessage());
        // Depending on how this function is used, you might throw the exception
        // throw $e;
        // or return null / false to indicate failure.
        return null;
    }
}

/**
 * Establishes a PDO database connection to a specific database.
 *
 * @param string $dbName The name of the database to connect to.
 * @return PDO|null Returns a PDO connection object on success, or null on failure.
 */
function get_db_connection_to_db($dbName = DB_DATABASE) {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . $dbName . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection to '$dbName' failed: " . $e->getMessage());
        return null;
    }
}

?>
