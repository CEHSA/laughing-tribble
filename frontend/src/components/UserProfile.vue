<template>
  <div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">User Profile</h2>

    <div v-if="isLoading" class="text-center text-gray-500">
      <p>Loading profile...</p>
      <!-- You can add a spinner here -->
    </div>

    <div v-if="error" class="mt-4 p-3 rounded-md bg-red-100 text-red-700">
      <p><strong>Error loading profile:</strong> {{ error }}</p>
    </div>

    <div v-if="user && !isLoading && !error">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium text-gray-500">Username</label>
          <p class="mt-1 text-lg text-gray-900">{{ user.username }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-500">Email</label>
          <p class="mt-1 text-lg text-gray-900">{{ user.email }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-500">Role</label>
          <p class="mt-1 text-lg text-gray-900">{{ user.role }}</p>
        </div>
        <div v-if="user.profile_picture">
          <label class="block text-sm font-medium text-gray-500">Profile Picture</label>
          <!-- For now, just display name. Later, could be an <img> tag -->
          <p class="mt-1 text-lg text-gray-900">{{ user.profile_picture }}</p>
        </div>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-500">Bio</label>
        <p class="mt-1 text-gray-700 whitespace-pre-line">{{ user.bio || 'No bio provided.' }}</p>
      </div>

      <div v-if="user.skills && user.skills.length > 0">
        <label class="block text-sm font-medium text-gray-500">Skills</label>
        <div class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="skill in user.skills"
            :key="skill"
            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm"
          >
            {{ skill }}
          </span>
        </div>
      </div>
       <div v-else>
        <label class="block text-sm font-medium text-gray-500">Skills</label>
        <p class="mt-1 text-gray-700">No skills listed.</p>
      </div>

      <div class="mt-8 text-center">
        <button
          @click="showUpdateForm = true"
          class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit Profile
        </button>
      </div>
    </div>

    <!-- Modal or separate view for UpdateProfileForm -->
    <div v-if="showUpdateForm" class="mt-6">
      <h3 class="text-2xl font-semibold text-center text-gray-800 mb-4">Update Your Profile</h3>
      <UpdateProfileForm :current-profile="user" @profile-updated="handleProfileUpdate" @cancel-update="showUpdateForm = false" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import authService from '../services/authService';
import UpdateProfileForm from './UpdateProfileForm.vue'; // Import the form component

const user = ref(null);
const isLoading = ref(true);
const error = ref('');
const showUpdateForm = ref(false);

const fetchUserProfile = async () => {
  isLoading.value = true;
  error.value = '';
  try {
    const response = await authService.getProfile();
    if (response.data && response.data.status === 'success') {
      user.value = response.data.user;
      console.log('Profile data:', user.value); // Added console.log
    } else {
      error.value = response.data?.message || 'Failed to fetch profile data.';
    }
  } catch (err) {
    error.value = err.response?.data?.message || err.message || 'An unexpected error occurred.';
    console.error('Error fetching profile:', err.response || err);
  } finally {
    isLoading.value = false;
  }
};

const handleProfileUpdate = (updatedUser) => {
  user.value = { ...user.value, ...updatedUser }; // Merge updated fields into local user object
  showUpdateForm.value = false;
  // Optionally, re-fetch or show a success message
  fetchUserProfile(); // Re-fetch to ensure data consistency if backend modifies/returns more fields
};

onMounted(fetchUserProfile);
</script>

<style scoped>
/* Scoped styles for UserProfile component */
.whitespace-pre-line {
  white-space: pre-line; /* To respect newlines in bio */
}
</style>
