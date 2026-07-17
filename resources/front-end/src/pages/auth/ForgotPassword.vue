<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-sm rounded border border-gray-200 bg-white p-8 shadow-sm">
      <h1 class="text-xl font-semibold text-gray-900">Reset your password</h1>
      <p class="mt-2 text-sm text-gray-600">
        Enter your email address and we’ll send you a secure reset link.
      </p>

      <div
        v-if="successMessage"
        class="mt-5 rounded border border-success-border bg-success-bg p-4 text-sm text-success"
        role="status"
      >
        {{ successMessage }}
      </div>
      <FormError v-if="error" :error="error" class="mt-5" />

      <form class="mt-5" @submit.prevent="submit">
        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
          Email address
        </label>
        <input
          id="email"
          v-model="email"
          type="email"
          autocomplete="email"
          required
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brass-500 focus:outline-none focus:ring-1 focus:ring-brass-500"
        />
        <button
          type="submit"
          :disabled="isLoading"
          class="mt-4 w-full rounded bg-brass px-4 py-2 text-sm font-semibold text-ink-900 hover:bg-brass-600 disabled:opacity-50"
        >
          {{ isLoading ? 'Sending…' : 'Send reset link' }}
        </button>
      </form>

      <router-link
        class="mt-5 block text-center text-sm font-medium text-brass-800 hover:underline"
        :to="{ name: 'login' }"
      >
        Back to login
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FormError from '@/components/FormError.vue';
import { useLoading } from '@/composables/useLoading';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const { isLoading, loadingQueue } = useLoading();
const email = ref('');
const error = ref(null);
const successMessage = ref('');

async function submit() {
  isLoading.value = true;
  loadingQueue.forgotPassword = false;
  error.value = null;
  successMessage.value = '';

  try {
    const { data } = await authStore.forgotPassword(email.value);
    successMessage.value = data.message;
  } catch (exception) {
    if (exception.response?.data?.errors) exception.errorBag = exception.response.data.errors;
    error.value = exception;
  } finally {
    loadingQueue.forgotPassword = true;
  }
}
</script>
