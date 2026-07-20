<template>
  <div class="w-full max-w-sm">
    <div class="auth-panel">
      <h1 class="auth-heading">Reset your password</h1>
      <p class="mt-2 text-sm text-ink-500">
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
        <label for="email" class="mb-1 block text-sm font-medium text-ink-700">
          Email address
        </label>
        <input
          id="email"
          v-model="email"
          type="email"
          autocomplete="email"
          required
          class="auth-input"
        />
        <button type="submit" :disabled="isLoading" class="auth-primary-action mt-4">
          {{ isLoading ? 'Sending…' : 'Send reset link' }}
        </button>
      </form>

      <router-link class="auth-link mt-5 block text-center text-sm" :to="{ name: 'login' }">
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
