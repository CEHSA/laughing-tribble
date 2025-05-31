<?php

require_once __DIR__ . '/config/database.php';

echo "Starting database setup...\n";

try {
    // 1. Connect to MySQL server (without specifying a database initially)
    $pdo = get_db_connection();

    if (!$pdo) {
        echo "Failed to connect to MySQL server. Please check your DB_HOST, DB_USERNAME, DB_PASSWORD in config/database.php.\n";
        exit(1);
    }
    echo "Successfully connected to MySQL server.\n";

    // 2. Create the database if it doesn't exist
    $dbName = DB_DATABASE; // from config/database.php
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database '$dbName' created or already exists.\n";

    // 3. Select the database (by reconnecting with dbname in DSN or using `USE`)
    // It's often cleaner to reconnect or use a dedicated function that includes dbname in DSN.
    $pdo = get_db_connection_to_db(); // This function includes DB_DATABASE in DSN
    if (!$pdo) {
        echo "Failed to connect to the database '$dbName'. Please check your configuration and server status.\n";
        exit(1);
    }
    echo "Successfully connected to database '$dbName'.\n";

    // Or, alternatively, after creating the DB with the first connection:
    // $pdo->exec("USE `$dbName`;");
    // echo "Selected database '$dbName'.\n";


    // 4. Create the 'users' table
    $sqlUsersTable = "
    CREATE TABLE IF NOT EXISTS `users` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(255) NOT NULL UNIQUE,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` VARCHAR(50) NOT NULL,
        `bio` TEXT NULL,
        `skills` TEXT NULL, -- Consider a separate skills table for better normalization
        `profile_picture_url` VARCHAR(255) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sqlUsersTable);
    echo "'users' table created or already exists.\n";

    // You could add more tables here in a similar fashion.
    // Example: Projects table
    /*
    $sqlProjectsTable = "
    CREATE TABLE IF NOT EXISTS `projects` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `client_id` INT NOT NULL, -- Foreign key to users table
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `status` VARCHAR(50) DEFAULT 'open', -- e.g., open, in_progress, completed, cancelled
        `budget` DECIMAL(10, 2) NULLABLE,
        `deadline` DATE NULLABLE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sqlProjectsTable);
    echo "'projects' table created or already exists.\n";
    */

    echo "Database setup completed successfully!\n";

} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
    // Log detailed error to a file in a real application
    error_log("Database setup PDOException: " . $e->getMessage());
    exit(1); // Exit with an error code
} catch (Exception $e) {
    echo "An unexpected error occurred: " . $e->getMessage() . "\n";
    error_log("Database setup Exception: " . $e->getMessage());
    exit(1);
}

?>
