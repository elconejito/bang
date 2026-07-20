<template>
  <div class="w-full max-w-sm">
    <div class="auth-panel">
      <div v-if="success">
        <p class="mb-4 text-sm text-ink-700">You have successfully registered. Please login now.</p>
        <router-link class="auth-link text-sm" :to="{ name: 'login' }">Login</router-link>
      </div>
      <div v-else>
        <h1 class="auth-heading mb-6">Register</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-4">
          <label for="name" class="mb-1 block text-sm font-medium text-ink-700">Name</label>
          <input type="text" class="auth-input" id="name" v-model="name" />
        </div>
        <div class="mb-4">
          <label for="email" class="mb-1 block text-sm font-medium text-ink-700"
            >Email Address</label
          >
          <input type="email" class="auth-input" id="email" v-model="email" />
        </div>
        <div class="mb-4">
          <label for="password" class="mb-1 block text-sm font-medium text-ink-700">Password</label>
          <input type="password" class="auth-input" id="password" v-model="password" />
        </div>
        <div class="mb-4">
          <label for="confirm_password" class="mb-1 block text-sm font-medium text-ink-700"
            >Confirm Password</label
          >
          <input
            type="password"
            class="auth-input"
            id="confirm_password"
            v-model="password_confirmation"
          />
        </div>
        <button class="auth-primary-action" @click="register" :disabled="isLoading">
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
