# Architex Axis - Frontend

This directory contains the Vue.js application for Architex Axis.

## Running Locally

To get the frontend running on your local machine for development:

1.  **Navigate to the frontend directory:**
    ```bash
    cd frontend
    ```
2.  **Install dependencies:** This will download all the necessary Node.js packages defined in `package.json`.
    ```bash
    npm install
    # or if you prefer yarn:
    # yarn install
    ```
3.  **Start the development server:**
    ```bash
    npm run serve
    # This command is typical for Vue CLI projects (which this seems to be based on package.json scripts like vue-cli-service serve).
    # If using Vite, it would be `npm run dev`. Assuming 'serve' is correct.
    ```
    This will usually start the application on a local development server, often at `http://localhost:8080` (for Vue CLI) or `http://localhost:5173` (for Vite). Check the terminal output for the exact URL.

## Building for Production

To build the application for production deployment:

1.  **Navigate to the frontend directory** (if not already there):
    ```bash
    cd frontend
    ```
2.  **Run the build script:**
    ```bash
    npm run build
    ```
    This command compiles and minifies the Vue.js application, typically outputting the static assets to a `dist/` directory within the `frontend` folder. These are the files you would deploy to a web server.

## Key Components & Services

*   **`src/App.vue`**: The main application shell that orchestrates different views and components.
*   **`src/components/RegisterForm.vue`**: Component providing the user interface for new user registration.
*   **`src/components/LoginForm.vue`**: Component providing the user interface for existing user login.
*   **`src/components/UserProfile.vue`**: Component for displaying the logged-in user's profile information.
*   **`src/components/UpdateProfileForm.vue`**: Component for allowing users to modify their profile details.
*   **`src/services/api.js`**: Configures and exports an Axios instance for making HTTP requests to the backend API.
*   **`src/services/authService.js`**: Handles authentication-related API calls (register, login, profile) and manages user session data (like user ID).
*   **`src/main.js`**: The entry point for the Vue application, where the root Vue instance is created and mounted.
*   **`public/index.html`**: The main HTML page that hosts the Vue application.
*   **`package.json`**: Defines project metadata, npm scripts (like `serve`, `build`), and dependencies.

This frontend is intended to interact with the PHP backend to provide a user interface for the Architex Axis application.
