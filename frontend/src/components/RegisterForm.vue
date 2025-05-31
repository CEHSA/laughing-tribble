<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register</h2>
    <form @submit.prevent="handleRegister">
      <div class="mb-4">
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input
          type="text"
          id="username"
          v-model="form.username"
          required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          v-model="form.email"
          required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input
          type="password"
          id="password"
          v-model="form.password"
          required
          minlength="6"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>
      <div class="mb-6">
        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
        <select
          id="role"
          v-model="form.role"
          required
          class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="Client">Client</option>
          <option value="Freelancer">Freelancer</option>
        </select>
      </div>
      <div>
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-indigo-300"
        >
          {{ isLoading ? 'Registering...' : 'Register' }}
        </button>
      </div>
    </form>
    <div v-if="message" class="mt-4 p-3 rounded-md" :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import authService from '../services/authService';

const form = ref({
  username: '',
  email: '',
  password: '',
  role: 'Client', // Default role
});

const isLoading = ref(false);
const message = ref('');
const messageType = ref(''); // 'success' or 'error'

const handleRegister = async () => {
  isLoading.value = true;
  message.value = '';
  messageType.value = '';
  try {
    const response = await authService.register(form.value);
    console.log('Registration response:', response.data); // Added console.log
    if (response.data && response.data.status === 'success') {
      message.value = response.data.message || 'Registration successful!';
      messageType.value = 'success';
      // Optionally, redirect or clear form:
      // form.value = { username: '', email: '', password: '', role: 'Client' };
    } else {
      // Handle cases where response.data.status is not 'success' or missing
      message.value = (response.data && response.data.message) || 'Registration failed. Please try again.';
      messageType.value = 'error';
    }
  } catch (error) {
    isLoading.value = false;
    const errorMsg = error.response?.data?.message || error.message || 'An unexpected error occurred.';
    message.value = `Registration failed: ${errorMsg}`;
    messageType.value = 'error';
    console.error('Registration error:', error.response || error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Scoped styles if needed, though Tailwind is preferred */
</style>
