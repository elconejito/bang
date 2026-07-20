<template>
  <div class="w-full max-w-sm">
    <div class="auth-panel">
      <div v-if="successMessage">
        <h1 class="auth-heading">Password reset</h1>
        <p class="mt-3 text-sm text-ink-500">{{ successMessage }}</p>
        <router-link class="auth-link mt-5 inline-block text-sm" :to="{ name: 'login' }">
          Continue to login
        </router-link>
      </div>

      <template v-else>
        <h1 class="auth-heading">Choose a new password</h1>
        <p class="mt-2 text-sm text-ink-500">Enter and confirm your new password.</p>
        <FormError v-if="error" :error="error" class="mt-5" />

        <form class="mt-5 space-y-4" @submit.prevent="submit">
          <div>
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
          </div>
          <div>
            <label for="password" class="mb-1 block text-sm font-medium text-ink-700">
              New password
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              autocomplete="new-password"
              required
              class="auth-input"
            />
          </div>
          <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-ink-700">
              Confirm new password
            </label>
            <input
              id="password_confirmation"
              v-model="passwordConfirmation"
              type="password"
              autocomplete="new-password"
              required
              class="auth-input"
            />
          </div>
          <button type="submit" :disabled="isLoading || !token" class="auth-primary-action">
            {{ isLoading ? 'Resetting…' : 'Reset password' }}
          </button>
        </form>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import FormError from '@/components/FormError.vue';
import { useLoading } from '@/composables/useLoading';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const authStore = useAuthStore();
const { isLoading, loadingQueue } = useLoading();
const token = String(route.query.token ?? '');
const email = ref(String(route.query.email ?? ''));
const password = ref('');
const passwordConfirmation = ref('');
const error = ref(token ? null : new Error('This password reset link is invalid.'));
const successMessage = ref('');

async function submit() {
  isLoading.value = true;
  loadingQueue.resetPassword = false;
  error.value = null;

  try {
    const { data } = await authStore.resetPassword({
      token,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    successMessage.value = data.message;
  } catch (exception) {
    if (exception.response?.data?.errors) exception.errorBag = exception.response.data.errors;
    error.value = exception;
  } finally {
    loadingQueue.resetPassword = true;
  }
}
</script>
