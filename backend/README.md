# Architex Axis - Backend

This directory contains the PHP API for Architex Axis.

## Overview

The backend is responsible for handling business logic, data processing, and providing API endpoints for the Vue.js frontend.

## Structure

*   **`api/`**: Contains the PHP files that define the API endpoints. For example, `api/users.php` might handle requests related to users.
*   **`config/`**: Intended for configuration files, such as database connection settings (`database.php`), API keys, or other environment-specific parameters.
*   **`index.php`**: Acts as the main entry point or router for incoming API requests. It directs requests to the appropriate endpoint handlers in the `api/` directory.
*   **`db_setup.php`**: A script to initialize the database schema (create database and tables).
*   **`.gitkeep` files**: Placeholder files in `api/` and `config/` to ensure these directories are tracked by Git even when initially empty.

## Setup Instructions

1.  **Database Setup**:
    *   Ensure you have a MySQL server running.
    *   Copy or configure `backend/config/database.php` with your MySQL connection details (host, database name, username, password). The default database name expected is `architex_axis_db`.
    *   Run the `backend/db_setup.php` script from your command line to create the database and the necessary `users` table:
        ```bash
        php backend/db_setup.php
        ```
        This script uses the credentials from `config/database.php`.

2.  **Web Server Configuration**:
    *   You'll need a web server (like Apache, Nginx) or you can use PHP's built-in server for development.
    *   **Apache/Nginx:** Point the server's document root to the `backend/` directory or configure it to serve `index.php` as the entry point. URL rewriting (e.g., via `.htaccess` for Apache, as described in `DEPLOYMENT_GUIDE.md`) is recommended for cleaner API URLs.
    *   **PHP Built-in Server (for development only):**
        Navigate to the `backend/` directory and run:
        ```bash
        php -S localhost:8000
        ```
        In this case, API URLs would be like `http://localhost:8000/api/users/register`. Ensure your frontend's `API_BASE_URL` matches this (e.g., `http://localhost:8000`). Note that path-based routing might require specific handling with the built-in server if not using a single entry point for all `/api/*` routes. The current `index.php` and `users.php` structure should work.

3.  **PHP Version**: Ensure your server environment is using a PHP version compatible with the application (e.g., PHP 8.0 or higher recommended). Required extensions include `pdo_mysql`, `json`, `mbstring`.

4.  **Dependencies**: Currently, the project does not use Composer for PHP package management. If external PHP libraries are added later, you would run `composer install` in this directory.

## API Endpoints

All user management endpoints are handled by `api/users.php` via the main `index.php` router. The base path for these endpoints is `/api/users`.

*   **`POST /api/users/register`**
    *   **Description:** Registers a new user.
    *   **Payload (JSON):**
        ```json
        {
          "username": "newuser",
          "email": "user@example.com",
          "password": "securepassword123",
          "role": "Client" // or "Freelancer"
        }
        ```
    *   **Responses:**
        *   `201 Created`: User registered successfully. Returns `{"status": "success", "message": "User registered successfully.", "user_id": id}`.
        *   `400 Bad Request`: Missing fields or invalid role.
        *   `409 Conflict`: Username or email already exists.
        *   `500 Internal Server Error`: Database or other server error.

*   **`POST /api/users/login`**
    *   **Description:** Logs in an existing user.
    *   **Payload (JSON):**
        ```json
        {
          "email": "user@example.com",
          "password": "securepassword123"
        }
        ```
    *   **Responses:**
        *   `200 OK`: Login successful. Returns `{"status": "success", "message": "Login successful.", "token": "fake-jwt-token-...", "user": {"id": 1, "username": "testuser", ...}}`.
        *   `400 Bad Request`: Missing email or password.
        *   `401 Unauthorized`: Invalid email or password.
        *   `500 Internal Server Error`.

*   **`GET /api/users/profile?user_id={id}`**
    *   **Description:** Retrieves a user's profile information.
    *   **URL Parameter:** `user_id` (integer) - The ID of the user whose profile is to be fetched.
    *   **Responses:**
        *   `200 OK`: Profile retrieved. Returns `{"status": "success", "user": {"id": 1, "username": "testuser", ...}}`.
        *   `400 Bad Request`: Missing or invalid `user_id`.
        *   `404 Not Found`: User not found.
        *   `500 Internal Server Error`.

*   **`PUT /api/users/profile`**
    *   **Description:** Updates an existing user's profile.
    *   **Payload (JSON):**
        ```json
        {
          "user_id": 1, // ID of the user to update
          "username": "updateduser", // Optional
          "email": "newemail@example.com", // Optional
          "bio": "Updated bio.", // Optional
          "skills": ["PHP", "Vue.js", "SQL"], // Optional, array of strings
          "profile_picture_url": "new_pic.jpg" // Optional
          // "new_password": "newsecurepassword" // Optional, for password change
        }
        ```
    *   **Responses:**
        *   `200 OK`: Profile updated successfully. Returns `{"status": "success", "message": "Profile updated successfully."}` or `{"status": "success", "message": "Profile data was the same, no changes made."}`.
        *   `400 Bad Request`: Missing `user_id` or no updatable fields provided.
        *   `404 Not Found`: User to update not found.
        *   `409 Conflict`: Username or email already exists (if changed to a conflicting value).
        *   `500 Internal Server Error`.

## Key Modules

*   **`index.php`**: Main router/entry point for the API. It handles incoming requests and delegates to the appropriate resource handlers (like `api/users.php`).
*   **`api/users.php`**: Handles all business logic for user-related actions: registration, login, profile viewing, and profile updates. Interacts directly with the database.
*   **`config/database.php`**: Contains database connection constants (host, database name, username, password) and provides a `get_db_connection_to_db()` function to establish a PDO connection.
*   **`db_setup.php`**: A command-line script used for initial database setup. It creates the specified database and the `users` table schema.

The backend is designed to work in conjunction with the Vue.js frontend to deliver the full Architex Axis application functionality.
