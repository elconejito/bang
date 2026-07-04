<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50">
    <div class="w-full max-w-sm rounded border border-gray-200 bg-white p-8 shadow-sm">
      <h1 class="mb-6 text-xl font-semibold text-gray-900">Login</h1>
      <FormError v-if="error" :error="error" />
      <form @submit.prevent="login">
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1"
            >Email address</label
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
        <button
          type="submit"
          class="w-full rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
          :disabled="isLoading"
        >
          Log in
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useLoading } from '@/composables/useLoading';
import FormError from '@/components/FormError.vue';

const router = useRouter();
const authStore = useAuthStore();
const { isLoading, loadingQueue } = useLoading();

const email = ref('');
const password = ref('');
const error = ref(null);

async function login() {
  isLoading.value = true;
  loadingQueue.login = false;
  error.value = null;
  try {
    await authStore.login({ email: email.value, password: password.value });
    router.push({ name: 'dashboard' });
  } catch (err) {
    error.value = err;
  } finally {
    loadingQueue.login = true;
  }
}
</script>
