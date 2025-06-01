<template>
  <div class="max-w-lg mx-auto p-6 bg-gray-50 rounded-lg shadow-md">
    <form @submit.prevent="handleUpdateProfile">
      <div class="mb-4">
        <label for="update-username" class="block text-sm font-medium text-gray-700">Username</label>
        <input
          type="text"
          id="update-username"
          v-model="form.username"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>

      <div class="mb-4">
        <label for="update-email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          id="update-email"
          v-model="form.email"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>

      <div class="mb-4">
        <label for="update-bio" class="block text-sm font-medium text-gray-700">Bio</label>
        <textarea
          id="update-bio"
          v-model="form.bio"
          rows="3"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        ></textarea>
      </div>

      <div class="mb-4">
        <label for="update-skills" class="block text-sm font-medium text-gray-700">
          Skills (comma-separated)
        </label>
        <input
          type="text"
          id="update-skills"
          v-model="skillsInput"
          @input="parseSkills"
          placeholder="e.g., Vue.js, PHP, TailwindCSS"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
         <div class="mt-1 text-xs text-gray-500">Enter skills separated by commas.</div>
      </div>

      <!-- Example for profile picture - simplified: text input for URL or name -->
      <div class="mb-6">
        <label for="update-profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture URL</label>
        <input
          type="text"
          id="update-profile_picture"
          v-model="form.profile_picture"
          placeholder="e.g., default.png or https://example.com/image.jpg"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>

      <div class="flex items-center justify-end space-x-3">
        <button
          type="button"
          @click="$emit('cancel-update')"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="isLoading"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:bg-indigo-300"
        >
          {{ isLoading ? 'Updating...' : 'Save Changes' }}
        </button>
      </div>
    </form>

    <div v-if="message" class="mt-4 p-3 rounded-md" :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'; // Removed defineProps, defineEmits
import authService from '../services/authService';

// defineProps and defineEmits are compiler macros, no import needed
const props = defineProps({
  currentProfile: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['profile-updated', 'cancel-update']);

const form = ref({
  username: '',
  email: '',
  bio: '',
  skills: [], // Stored as an array
  profile_picture: '',
});

const skillsInput = ref(''); // For comma-separated string input

const isLoading = ref(false);
const message = ref('');
const messageType = ref(''); // 'success' or 'error'

// Initialize form with current profile data
const initializeForm = (profile) => {
  if (profile) {
    form.value.username = profile.username || '';
    form.value.email = profile.email || '';
    form.value.bio = profile.bio || '';
    form.value.skills = Array.isArray(profile.skills) ? [...profile.skills] : [];
    skillsInput.value = form.value.skills.join(', ');
    form.value.profile_picture = profile.profile_picture || '';
  }
};

// Call initializeForm when the component is mounted and when currentProfile prop changes
onMounted(() => initializeForm(props.currentProfile));
watch(() => props.currentProfile, (newProfile) => {
  initializeForm(newProfile);
}, { deep: true });


const parseSkills = () => {
  form.value.skills = skillsInput.value.split(',').map(skill => skill.trim()).filter(skill => skill);
};

const handleUpdateProfile = async () => {
  isLoading.value = true;
  message.value = '';
  messageType.value = '';

  // Ensure skills are parsed before submitting
  parseSkills();

  // Filter out fields that haven't changed or are empty, if desired by backend
  // For now, sending all fields that are part of the form.
  const profileDataToUpdate = { ...form.value };

  try {
    const response = await authService.updateProfile(profileDataToUpdate);
    console.log('Update profile response:', response.data); // Added console.log
    if (response.data && response.data.status === 'success') {
      message.value = response.data.message || 'Profile updated successfully!';
      messageType.value = 'success';
      emit('profile-updated', response.data.updated_fields || profileDataToUpdate); // Emit event with updated data
    } else {
      message.value = (response.data && response.data.message) || 'Update failed. Please try again.';
      messageType.value = 'error';
    }
  } catch (error) {
    const errorMsg = error.response?.data?.message || error.message || 'An unexpected error occurred.';
    message.value = `Update failed: ${errorMsg}`;
    messageType.value = 'error';
    console.error('Update profile error:', error.response || error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
/* Scoped styles if needed */
</style>
