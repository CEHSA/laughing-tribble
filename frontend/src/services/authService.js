import apiClient from './api';

const API_ENDPOINT_PREFIX = '/api/users';
const USER_TOKEN_KEY = 'user-token';
const USER_ID_KEY = 'user-id';

// In-memory store (optional, localStorage is primary for persistence)
let currentToken = localStorage.getItem(USER_TOKEN_KEY);
let currentUserId = localStorage.getItem(USER_ID_KEY);


// Helper functions for managing user session
const storeUserSession = (token, userId) => {
  localStorage.setItem(USER_TOKEN_KEY, token);
  localStorage.setItem(USER_ID_KEY, userId);
  currentToken = token;
  currentUserId = userId;
  // If using Axios interceptor for token, update it here if not already dynamic
  // apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`;
};

const clearUserSession = () => {
  localStorage.removeItem(USER_TOKEN_KEY);
  localStorage.removeItem(USER_ID_KEY);
  currentToken = null;
  currentUserId = null;
  // If using Axios interceptor, clear token
  // delete apiClient.defaults.headers.common['Authorization'];
};

const getUserId = () => {
  return currentUserId || localStorage.getItem(USER_ID_KEY);
};

// const getToken = () => {
//   return currentToken || localStorage.getItem(USER_TOKEN_KEY);
// };


const authService = {
  register(userData) {
    return apiClient.post(`${API_ENDPOINT_PREFIX}/register`, userData);
  },

  async login(credentials) {
    const response = await apiClient.post(`${API_ENDPOINT_PREFIX}/login`, credentials);
    if (response.data && response.data.status === 'success' && response.data.token && response.data.user?.id) {
      storeUserSession(response.data.token, response.data.user.id.toString());
    }
    return response; // Return the full response for the component to handle
  },

  logout() {
    clearUserSession();
    // Potentially call a backend endpoint to invalidate session/token if applicable
    // return apiClient.post('/api/users/logout');
    return Promise.resolve(); // Simulate immediate logout for now
  },

  getProfile() {
    const userId = getUserId();
    if (!userId) {
      return Promise.reject(new Error('User not logged in or user ID not found.'));
    }
    // Backend expects user_id as a query parameter
    return apiClient.get(`${API_ENDPOINT_PREFIX}/profile?user_id=${userId}`);
  },

  updateProfile(profileData) {
    const userId = getUserId();
    if (!userId) {
      return Promise.reject(new Error('User not logged in or user ID not found for update.'));
    }
    // Backend expects user_id in the request body for updates
    const dataWithUserId = { ...profileData, user_id: parseInt(userId, 10) };
    return apiClient.put(`${API_ENDPOINT_PREFIX}/profile`, dataWithUserId);
  },

  // Helper to check if user is authenticated (basic check)
  isAuthenticated() {
    return !!getUserId(); // And possibly check token validity
  },

  // Helper to get current user ID directly if needed by other parts of the app
  getCurrentUserId() {
    return getUserId();
  }
};

// Initialize: check if session exists from previous page load
// This is implicitly handled by initializing currentToken and currentUserId from localStorage.

export default authService;
