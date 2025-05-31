<template>
  <div id="app" class="min-h-screen bg-gray-100 py-8">
    <header class="text-center mb-10">
      <h1 class="text-4xl font-bold text-indigo-700">Architex Axis</h1>
      <p class="text-lg text-gray-600">Connecting Clients and Freelancers Seamlessly</p>
    </header>

    <nav v-if="isAuthenticated" class="container mx-auto px-4 mb-6 flex justify-end">
      <button
        @click="handleLogout"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
      >
        Logout
      </button>
    </nav>

    <main class="container mx-auto px-4">
      <div v-if="!isAuthenticated" class="grid md:grid-cols-2 gap-12">
        <div>
          <RegisterForm />
        </div>
        <div>
          <!-- Listen for login-success event from LoginForm -->
          <LoginForm @login-success="handleLoginSuccess" />
        </div>
      </div>
      <div v-else>
        <!-- UserProfile will fetch its own data for now -->
        <UserProfile />
      </div>
    </main>

    <footer class="text-center mt-12 py-4 text-gray-500">
      <p>&copy; {{ new Date().getFullYear() }} Architex Axis. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'; // Import onMounted
import RegisterForm from './components/RegisterForm.vue';
import LoginForm from './components/LoginForm.vue';
import UserProfile from './components/UserProfile.vue';
import authService from './services/authService'; // Import authService

const isAuthenticated = ref(authService.isAuthenticated()); // Initialize from service
const currentUser = ref(null); // Could store more detailed user info if needed

// This function will be called when LoginForm emits 'login-success'
// The event payload from LoginForm isn't strictly needed here anymore since
// authService.login now handles storing the session data.
const handleLoginSuccess = (/* loginData */) => {
  isAuthenticated.value = true;
  // Optionally, fetch user details here to populate currentUser if needed globally in App.vue
  // For example:
  // const userId = authService.getCurrentUserId();
  // if (userId) {
  //   authService.getProfile().then(response => {
  //     currentUser.value = response.data.user;
  //   }).catch(error => console.error("Failed to fetch profile for App.vue", error));
  // }
  console.log('Login successful in App.vue, user ID:', authService.getCurrentUserId());
};

const handleLogout = async () => {
  await authService.logout(); // This clears stored session data in authService
  isAuthenticated.value = false;
  currentUser.value = null;
  console.log('Logged out from App.vue');
  // Potentially redirect to login page or home page
};

// Check authentication status when the component is mounted
onMounted(() => {
  isAuthenticated.value = authService.isAuthenticated();
  // If authenticated, you might want to load current user details if needed by App.vue itself
  // if (isAuthenticated.value) {
  //   const userId = authService.getCurrentUserId();
  //   // Fetch user details if necessary, e.g. for a global user display name
  // }
  console.log('App.vue mounted, isAuthenticated:', isAuthenticated.value);
});

</script>

<style>
/* Global styles or Tailwind import if not handled by main.js's index.css */
.container {
  max-width: 1280px;
}
</style>
