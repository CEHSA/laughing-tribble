import axios from 'axios';

// Determine the base URL for the API.
// This can be made more dynamic for different environments (dev, prod).
// For local development, assuming backend is served at /architex-axis/backend/
// If your PHP backend is directly at the root of a domain (e.g., http://localhost:8000),
// then baseURL would be 'http://localhost:8000/api' or similar,
// depending on how backend routes are structured.

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL || 'http://localhost/architex-axis/backend';
// Note: If you use PHP's built-in server `php -S localhost:8000 -t backend/`,
// and your index.php is in backend/, requests would be like http://localhost:8000/api/users/register
// In that case, baseURL might be 'http://localhost:8000' and endpoints would include '/api/users'.
// For now, sticking to the problem description's hint.

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    // 'X-Requested-With': 'XMLHttpRequest', // Often good for PHP backends to identify AJAX
  }
});

// You can add interceptors here if needed (e.g., for token handling)
apiClient.interceptors.request.use(config => {
  // Example: Get token from localStorage and add to headers
  // const token = localStorage.getItem('user-token');
  // if (token) {
  //   config.headers.Authorization = `Bearer ${token}`;
  // }
  return config;
}, error => {
  return Promise.reject(error);
});

apiClient.interceptors.response.use(response => {
  return response;
}, error => {
  // Handle global errors here
  // For example, redirect to login on 401
  // if (error.response && error.response.status === 401) {
  //   // router.push('/login'); // Assuming you have Vue router instance
  // }
  return Promise.reject(error);
});

export default apiClient;
