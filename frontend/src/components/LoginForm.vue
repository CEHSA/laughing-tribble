<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>
    <form @submit.prevent="handleLogin">
      <div class="mb-4">
        <label for="login-email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="login-email"
          v-model="form.email"
          required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-6">
        <label for="login-password" class="block text-sm font-medium text-gray-700">Password</label>
        <input
          type="password"
          id="login-password"
          v-model="form.password"
          required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div>
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:bg-green-300"
        >
          {{ isLoading ? 'Logging in...' : 'Login' }}
        </button>
      </div>
    </form>
    <div v-if="message" class="mt-4 p-3 rounded-md" :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
      {{ message }}
    </div>
     <div v-if="token" class="mt-4 p-3 rounded-md bg-blue-100 text-blue-700 break-all">
      <strong>Token:</strong> {{ token }}
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue'; // Import defineEmits
import authService from '../services/authService';

// Declare emits
const emit = defineEmits(['login-success']);

const form = ref({
  email: '',
  password: '',
});

const isLoading = ref(false);
const message = ref('');
const messageType = ref(''); // 'success' or 'error'
const token = ref(''); // To display the fake token

const handleLogin = async () => {
  isLoading.value = true;
  message.value = '';
  messageType.value = '';
  token.value = '';

  try {
    const response = await authService.login(form.value);
    console.log('Login response:', response.data); // Added console.log
    if (response.data && response.data.status === 'success') {
      message.value = response.data.message || 'Login successful!';
      messageType.value = 'success';
      if (response.data.token) {
        token.value = response.data.token;
        // In a real app, store the token:
        // localStorage.setItem('user-token', response.data.token);
        // And update global state (e.g., Vuex/Pinia)
      }
      // Emit the login-success event, optionally with user data
      emit('login-success', { token: response.data.token /*, user: response.data.user */ });
      // Optionally, redirect:
      // router.push('/dashboard');
    } else {
      message.value = (response.data && response.data.message) || 'Login failed. Please check your credentials.';
      messageType.value = 'error';
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || error.message || 'An unexpected error occurred.';
    message.value = `Login failed: ${errorMsg}`;
    messageType.value = 'error';
    console.error('Login error:', error.response || error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Scoped styles if needed */
</style>
