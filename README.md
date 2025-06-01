# Architex Axis

Welcome to Architex Axis! This project is a web application designed as an admin-centric marketplace for architectural services. It connects clients needing architectural work with freelance architects, providing a platform for project posting, bidding (future), and user management.

It consists of two main components:

1.  **Frontend**: A Vue.js single-page application (SPA) located in the `frontend/` directory. This provides the user interface and interacts with the backend API.
2.  **Backend**: A PHP-based API located in the `backend/` directory. This handles business logic, data storage, and serves data to the frontend.

## Project Structure

```
.
├── frontend/         # Vue.js frontend application
│   ├── public/
│   ├── src/
│   ├── package.json
│   └── README.md
├── backend/          # PHP backend API
│   ├── api/          # API endpoint files
│   ├── config/       # Configuration files
│   ├── index.php     # Main API router/entry point
│   └── README.md
└── README.md         # This file (project root README)
```

## Project Overview

Architex Axis is a full-stack web application featuring:
*   **Frontend**: A dynamic user interface built with Vue.js, allowing users to register, log in, view, and update their profiles.
*   **Backend**: A PHP-based API that handles user authentication, profile management, and (in the future) project and bid management. It uses a MySQL database for data persistence.

The platform aims to be an admin-centric marketplace facilitating connections between clients seeking architectural services and freelance architects.

## Getting Started

To get the Architex Axis application running locally or for development:

1.  **Clone the Repository:**
    ```bash
    git clone <your-repository-url>
    cd architex-axis
    # Or your project's root directory name
    ```
2.  **Set Up the Backend:**
    *   Detailed instructions are in `backend/README.md`. This involves setting up your PHP environment, MySQL database (using `db_setup.php`), and configuring database connection details in `backend/config/database.php`.

3.  **Set Up the Frontend:**
    *   Detailed instructions are in `frontend/README.md`. This involves installing Node.js dependencies (`npm install`) and running the Vue.js development server (`npm run serve`).

## Deployment

For deploying this application to a hosting environment, please refer to the `DEPLOYMENT_GUIDE.md`.

**Automated Deployment with cPanel:**
This project includes a `.cpanel.yml` file designed for use with cPanel's "Git Version Control" feature. If your cPanel hosting supports this, it can automate the deployment process whenever you push changes to your connected repository. See the `.cpanel.yml` file and the `DEPLOYMENT_GUIDE.md` for more details on configuring this.

## Documentation

For more detailed information about specific parts of the project, please refer to the following documents:

*   **Frontend Application:** See `frontend/README.md` for setup, running, building, and component/service details.
*   **Backend API:** See `backend/README.md` for API endpoint descriptions, setup instructions, and key module explanations.
*   **Deployment Guide:** See `DEPLOYMENT_GUIDE.md` for comprehensive instructions on deploying the application to a cPanel hosting environment.

## Basic Maintenance Plan

To ensure the smooth operation and longevity of the Architex Axis application, consider the following maintenance practices:

*   **Dependency Management**:
    *   **Frontend (npm):** Regularly review and update npm packages in the `frontend/` directory using `npm outdated` and `npm update`. Test thoroughly after updates.
    *   **Backend (PHP/Composer):** Review the PHP version for security support. If Composer is introduced later for PHP dependencies, update packages using `composer outdated` and `composer update`.
*   **Monitoring**:
    *   Periodically check server error logs (e.g., Apache, Nginx, PHP-FPM error logs) for any unhandled issues.
    *   Consider implementing application-level logging for critical errors or important transactions if the application grows in complexity.
*   **Database Backups**:
    *   Schedule regular backups of the MySQL database (`architex_axis_db`). Most hosting providers (like cPanel) offer tools for automated backups. If not, implement a script using `mysqldump`.
    *   Periodically test restoring backups to ensure their integrity.
*   **Security**:
    *   Stay informed about security vulnerabilities related to PHP, MySQL, Vue.js, Node.js, and any third-party libraries used.
    *   Apply security patches and updates promptly.
    *   Continue to follow security best practices in development:
        *   Validate all user inputs (client-side and server-side).
        *   Use prepared statements for all SQL queries (as currently implemented with PDO).
        *   Properly sanitize or encode output to prevent XSS attacks.
        *   Implement CSRF protection if forms become more complex or state-changing GET requests are used.
        *   Regularly review user roles and permissions.
*   **Version Control**:
    *   Use Git for all code changes, bug fixes, and new features.
    *   Maintain a clear and descriptive commit history.
    *   Utilize branches for developing new features or fixing bugs to keep the main branch stable.
    *   Regularly push changes to a remote repository.
*   **Issue Tracking**:
    *   For ongoing development and maintenance, use an issue tracking system (e.g., GitHub Issues, GitLab Issues, Jira) to log bugs, feature requests, and tasks.

---

*This project structure was initialized and developed by an AI agent.*
