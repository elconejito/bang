<template>
  <div class="w-full max-w-sm">
    <div class="w-full max-w-sm rounded border border-gray-200 bg-white p-8 shadow-sm">
      <div v-if="successMessage">
        <h1 class="text-xl font-semibold text-gray-900">Password reset</h1>
        <p class="mt-3 text-sm text-gray-600">{{ successMessage }}</p>
        <router-link
          class="mt-5 inline-block text-sm font-semibold text-brass-800 hover:underline"
          :to="{ name: 'login' }"
        >
          Continue to login
        </router-link>
      </div>

      <template v-else>
        <h1 class="text-xl font-semibold text-gray-900">Choose a new password</h1>
        <p class="mt-2 text-sm text-gray-600">Enter and confirm your new password.</p>
        <FormError v-if="error" :error="error" class="mt-5" />

        <form class="mt-5 space-y-4" @submit.prevent="submit">
          <div>
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
          </div>
          <div>
            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
              New password
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              autocomplete="new-password"
              required
              class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brass-500 focus:outline-none focus:ring-1 focus:ring-brass-500"
            />
          </div>
          <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">
              Confirm new password
            </label>
            <input
              id="password_confirmation"
              v-model="passwordConfirmation"
              type="password"
              autocomplete="new-password"
              required
              class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-brass-500 focus:outline-none focus:ring-1 focus:ring-brass-500"
            />
          </div>
          <button
            type="submit"
            :disabled="isLoading || !token"
            class="w-full rounded bg-brass px-4 py-2 text-sm font-semibold text-ink-900 hover:bg-brass-600 disabled:opacity-50"
          >
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
