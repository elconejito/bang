<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50">
    <div class="w-full max-w-sm rounded border border-gray-200 bg-white p-8 shadow-sm">
      <div v-if="success">
        <p class="mb-4 text-sm text-gray-700">
          You have successfully registered. Please login now.
        </p>
        <router-link class="text-sm text-blue-600 hover:underline" :to="{ name: 'login' }"
          >Login</router-link
        >
      </div>
      <div v-else>
        <h1 class="mb-6 text-xl font-semibold text-gray-900">Register</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input
            type="text"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="name"
            v-model="name"
          />
        </div>
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1"
            >Email Address</label
          >
          <input
            type="email"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="email"
            v-model="email"
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1"
            >Password</label
          >
          <input
            type="password"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="password"
            v-model="password"
          />
        </div>
        <div class="mb-4">
          <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1"
            >Confirm Password</label
          >
          <input
            type="password"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="confirm_password"
            v-model="password_confirmation"
          />
        </div>
        <button
          class="w-full rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
          @click="register"
          :disabled="isLoading"
        >
          Register
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useLoading } from '@/composables/useLoading';
import FormError from '@/components/FormError.vue';

const authStore = useAuthStore();
const { isLoading, loadingQueue } = useLoading();

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const error = ref(null);
const success = ref(false);

async function register() {
  isLoading.value = true;
  loadingQueue.register = false;
  error.value = null;
  try {
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    loadingQueue.register = true;
    success.value = true;
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
    loadingQueue.register = true;
  }
}
</script>
