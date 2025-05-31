# Architex Axis Deployment Guide (cPanel)

This guide provides step-by-step instructions for deploying the Architex Axis application (Vue.js frontend and PHP backend) to a shared cPanel hosting environment.

## I. Prerequisites

Before you begin, ensure you have the following:

*   **cPanel Hosting Account Access:** Credentials to log in to your cPanel account.
*   **FTP Client or cPanel File Manager Access:** For uploading files (e.g., FileZilla, Cyberduck, or the built-in cPanel File Manager).
*   **phpMyAdmin Access:** Available through cPanel for database management.
*   **Git Version Control:** Familiarity with Git. Your cPanel might offer Git integration, or you might use it for local management and FTP for uploads.
*   **Node.js/npm Access:** Installed on your local machine (or wherever you build the Vue.js application) to prepare the frontend assets.

## II. Backend Deployment (PHP API)

Follow these steps to deploy the PHP backend:

### 1. Database Setup

*   **A. Export Local Database:**
    *   Using your local database management tool (e.g., phpMyAdmin, MySQL Workbench, or `mysqldump` CLI):
        *   Select your development database (e.g., `architex_axis_db`).
        *   Export the database structure and data to an SQL file (e.g., `architex_axis_dump.sql`).
        *   Example using `mysqldump`:
            ```bash
            mysqldump -u your_local_user -p your_local_db_name > architex_axis_dump.sql
            ```

*   **B. Create Database and User in cPanel:**
    1.  Log in to your cPanel account.
    2.  Navigate to **"MySQL® Databases"** (or similar).
    3.  **Create a New Database:** Enter a name for your database (e.g., `architex_axis_live`). cPanel often prefixes this with your cPanel username (e.g., `cpaneluser_architex_axis_live`). Note this full database name.
    4.  **Create a New MySQL User:** Scroll down to "MySQL Users." Create a new user (e.g., `axis_user`). Use a strong password and note it down. cPanel also prefixes the username (e.g., `cpaneluser_axis_user`).
    5.  **Add User to Database:** Scroll to "Add User To Database." Select the newly created user and the newly created database. Click "Add."
    6.  **Assign Privileges:** Grant the user "All Privileges" for this database. Click "Make Changes."

*   **C. Import SQL File via phpMyAdmin:**
    1.  In cPanel, navigate to **"phpMyAdmin."**
    2.  Select the newly created database (e.g., `cpaneluser_architex_axis_live`) from the left-hand sidebar.
    3.  Click on the **"Import"** tab.
    4.  Click **"Choose File"** and select your exported `architex_axis_dump.sql` file.
    5.  Ensure the character set is `utf8mb4` if that's what your local DB used.
    6.  Click **"Go"** (or "Import") to start the import process.

### 2. Upload Backend Files

Choose one of the following methods:

*   **A. Using Git (if cPanel supports it or via SSH):**
    1.  Ensure all backend files (`backend/` directory contents) are committed to your Git repository.
    2.  **cPanel Git Version Control:**
        *   In cPanel, look for "Git™ Version Control" or similar.
        *   Create a new repository or clone an existing one.
        *   Enter your repository URL (e.g., from GitHub, GitLab).
        *   Specify the deployment path. This is where the repository will be cloned on the server (e.g., `public_html/api_backend` or a specific subdomain's document root like `api.yourdomain.com`).
        *   Deploy (checkout) the appropriate branch (e.g., `main` or `production`).
    3.  **SSH Access:**
        *   If you have SSH access, connect to your server.
        *   Navigate to the desired deployment directory.
        *   Clone your repository: `git clone your_repository_url .` (the `.` clones into the current directory).
        *   Checkout the deployment branch: `git checkout main`.

*   **B. Using FTP/File Manager:**
    1.  Connect to your server using an FTP client or open the cPanel File Manager.
    2.  Navigate to the desired directory on the server where you want to host the backend (e.g., `public_html/api_backend`, or a subdomain's document root).
    3.  Upload all files and folders from your local `backend/` directory.
        *   **Important:** If you are not using Git on the server, consider excluding the `.git/` directory and any local development-specific files from your upload.

### 3. Configure Backend

*   **A. Update `database.php`:**
    1.  On the server, navigate to the uploaded backend files.
    2.  Edit the `backend/config/database.php` file.
    3.  Update the database connection constants with the cPanel database details you noted earlier:
        ```php
        define('DB_HOST', 'localhost'); // Usually 'localhost' for cPanel
        define('DB_DATABASE', 'cpaneluser_architex_axis_live'); // Your cPanel DB name
        define('DB_USERNAME', 'cpaneluser_axis_user');      // Your cPanel DB user
        define('DB_PASSWORD', 'your_strong_password');    // Password for the DB user
        ```
*   **B. File Permissions:**
    *   Ensure directories are typically set to `755` and files to `644`. This is usually the default and correct for cPanel environments. If you encounter permission issues, you can adjust these using the cPanel File Manager or an FTP client.

### 4. Configure Web Server (Apache via `.htaccess`)

If your backend is in a subdirectory (e.g., `public_html/api_backend/`) and you want clean URLs (e.g., `yourdomain.com/api_backend/users/login` instead of `yourdomain.com/api_backend/index.php/users/login`), you'll need an `.htaccess` file.

1.  In the root directory of your backend deployment on the server (e.g., `public_html/api_backend/`), create or edit an `.htaccess` file.
2.  Add the following rules. **Note:** If your backend is in a subdirectory, `RewriteBase` might be needed if not automatically handled.
    ```apache
    <IfModule mod_rewrite.c>
      RewriteEngine On

      # If your API is in a subdirectory of the domain (e.g. yourdomain.com/api_backend/)
      # you might need to set RewriteBase.
      # Example: If API URL is yourdomain.com/api_backend/, set:
      # RewriteBase /api_backend/

      # Redirect Trailing Slashes If Not A Folder...
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^(.*)/$ /$1 [L,R=301] # Use with caution if RewriteBase is also used.

      # Handle Front Controller...
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^ index.php [QSA,L]

      # Handle OPTIONS requests for CORS preflight (if not handled in PHP)
      RewriteCond %{REQUEST_METHOD} OPTIONS
      RewriteRule ^(.*)$ $1 [R=200,L]
    </IfModule>

    # Optional: Set environment variables if needed by PHP
    # SetEnv APP_ENV production

    # Optional: Improve security by denying access to sensitive files
    <Files "config/database.php">
        Require all denied
    </Files>
    <Files "db_setup.php">
        Require all denied
    </Files>
    <Files ".env"> # If you use .env files
        Require all denied
    </Files>
    ```
    *   The `RewriteRule ^ index.php [QSA,L]` routes all non-file/non-directory requests to your main `index.php` (which should be in the same directory as this `.htaccess`).
    *   The `OPTIONS` rule can help with CORS pre-flight requests if not explicitly handled in your PHP entry script for all paths.

### 5. Set PHP Version

1.  In cPanel, find an option like **"MultiPHP Manager"** or **"Select PHP Version."**
2.  Navigate to the domain or subdomain where you deployed the backend.
3.  Select a PHP version that is compatible with your application (e.g., PHP 8.1, 8.2, 8.3, as used in development).
4.  Ensure necessary PHP extensions (like `pdo_mysql`, `json`, `mbstring`) are enabled. Usually, these are enabled by default with standard PHP versions in cPanel.

## III. Frontend Deployment (Vue.js Application)

### 1. Build Vue.js Application

1.  **On your local machine:**
    *   Navigate to your `frontend/` directory.
    *   **Crucial:** Update the API base URL.
        *   If you are using an environment variable (e.g., `VUE_APP_API_BASE_URL` in `.env.production`):
            Create/edit `frontend/.env.production` file:
            ```
            VUE_APP_API_BASE_URL=https://yourdomain.com/api_backend
            # Or whatever the live URL of your PHP backend is
            ```
        *   If hardcoded in `frontend/src/services/api.js`, change it directly:
            ```javascript
            const API_BASE_URL = 'https://yourdomain.com/api_backend';
            ```
    *   Run the production build command:
        ```bash
        npm run build
        ```
        This command will compile and minify your Vue.js application into static assets, typically placed in a `frontend/dist/` directory.

### 2. Upload Frontend Files

1.  Using an FTP client or cPanel File Manager:
    *   Navigate to the directory on your server where you want to host the frontend. This is usually:
        *   `public_html/` for your main domain (e.g., `https://yourdomain.com`).
        *   A specific subdomain's document root (e.g., `subdomain.yourdomain.com/`).
    *   Upload **the contents** of your local `frontend/dist/` directory (e.g., `index.html`, `css/`, `js/` subdirectories) to this server location.

### 3. Configure Web Server for SPA Routing (Apache via `.htaccess`)

For Single Page Applications (SPAs) like Vue.js that use HTML5 history mode for routing, you need to configure the server to redirect all route requests to your main `index.html` file.

1.  In the root directory where you uploaded the frontend files (e.g., `public_html/` or your subdomain's root), create or edit an `.htaccess` file.
2.  Add the following rules:
    ```apache
    <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteBase / # If your app is in the root, otherwise e.g. /my-vue-app/
      RewriteRule ^index\.html$ - [L]
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule . /index.html [L]
    </IfModule>
    ```
    This ensures that any direct navigation or refresh to a Vue route (e.g., `yourdomain.com/profile`) is handled by `index.html`, allowing Vue Router to take over.

## IV. Post-Deployment Checklist

1.  **Test Application:**
    *   Open your live domain/subdomain in a browser.
    *   Test user registration.
    *   Test user login and logout.
    *   Test fetching and updating user profiles.
    *   Test any other core functionalities.
2.  **Check URLs:** Verify that both frontend and backend API URLs are correct and accessible.
3.  **Browser Developer Console:** Open your browser's developer tools (usually F12) and check:
    *   **Console Tab:** For any JavaScript errors.
    *   **Network Tab:** For any failed API requests (404s, 500s, CORS errors).
4.  **Database Connectivity:** Ensure actions that involve the database (registration, login, profile view/update) are working.
5.  **HTTPS:** If you have an SSL certificate for your domain:
    *   Ensure the site loads correctly via `https://`.
    *   Check for "mixed content" warnings (e.g., API calls being made over HTTP from an HTTPS frontend). Update your API base URL to use HTTPS if necessary.
6.  **Error Logs:**
    *   In cPanel, look for "Errors" or "Error Log" to view Apache error logs.
    *   PHP error logs might also be available or configured to log within your application's directory (though this is less common for production unless specified).

## V. Troubleshooting Common Issues

*   **CORS Errors:**
    *   **Symptom:** Frontend requests to the backend fail with messages like "Cross-Origin Request Blocked."
    *   **Solution:**
        *   Ensure `Access-Control-Allow-Origin` in your PHP backend (`users.php` or `index.php`) is set correctly. For production, it should ideally be your frontend's domain (e.g., `https://yourdomain.com`) instead of `*`.
        *   Verify `Access-Control-Allow-Methods` and `Access-Control-Allow-Headers` are sufficient.
        *   If backend and frontend are on different subdomains (e.g., `app.yourdomain.com` and `api.yourdomain.com`), this is a cross-origin scenario.

*   **404 Errors for SPA Routes (Frontend):**
    *   **Symptom:** Navigating directly to a Vue route (e.g., `yourdomain.com/user/profile`) results in a 404 error from the server. The main page `yourdomain.com` loads, but sub-routes don't.
    *   **Solution:** Double-check the `.htaccess` rules in your frontend's root directory. Ensure it's correctly redirecting all non-file/non-directory requests to `index.html`.

*   **404 Errors for API Routes (Backend):**
    *   **Symptom:** Frontend gets 404 errors when trying to reach API endpoints.
    *   **Solution:**
        *   Verify the `API_BASE_URL` in your frontend configuration is correct.
        *   Check the `.htaccess` rules in your backend directory. Ensure `RewriteBase` is correct if needed.
        *   Make sure the PHP files (`index.php`, `api/users.php`) were uploaded correctly.

*   **500 Internal Server Errors (Backend):**
    *   **Symptom:** API requests return a 500 error.
    *   **Solution:**
        *   **Check PHP Error Logs:** This is the most important step. cPanel's "Error Log" section or specific PHP error logs can provide details.
        *   **Database Connection:** Verify `backend/config/database.php` has the correct live database credentials and that the database user has the necessary permissions.
        *   **File Permissions:** Ensure PHP scripts have readable permissions.
        *   **PHP Version/Extensions:** Confirm the PHP version and required extensions are active on the server.
        *   **`.htaccess` Syntax:** Errors in `.htaccess` can also cause 500 errors.

*   **"White Screen of Death" (Frontend Vue App):**
    *   **Symptom:** The Vue app doesn't load, showing a blank white screen.
    *   **Solution:**
        *   Check the browser's JavaScript console for errors. This is often due to a JavaScript error during app initialization.
        *   Ensure all build files from `frontend/dist/` were uploaded correctly and paths in `index.html` to JS/CSS files are correct (usually relative and should work fine).
        *   Verify the `API_BASE_URL` is correctly configured; sometimes an app might hang if it can't reach its API on startup for initial data.

This guide should help you deploy Architex Axis. Remember that server configurations can vary, so some adjustments might be necessary based on your specific cPanel setup. Always back up your files and database before making significant changes. Good luck!
