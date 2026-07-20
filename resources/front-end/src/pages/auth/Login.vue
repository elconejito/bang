<template>
  <div class="w-full max-w-sm">
    <div class="auth-panel">
      <h1 class="auth-heading mb-6">Login</h1>
      <FormError v-if="error" :error="error" />
      <form @submit.prevent="login">
        <div class="mb-4">
          <label for="email" class="mb-1 block text-sm font-medium text-ink-700"
            >Email address</label
          >
          <input type="email" class="auth-input" id="email" v-model="email" />
        </div>
        <div class="mb-4">
          <div class="mb-1 flex items-center justify-between gap-3">
            <label for="password" class="block text-sm font-medium text-ink-700">Password</label>
            <router-link
              v-if="authStore.passwordResetEnabled"
              class="auth-link text-sm"
              :to="{ name: 'forgotPassword' }"
            >
              Forgot password?
            </router-link>
          </div>
          <input type="password" class="auth-input" id="password" v-model="password" />
        </div>
        <button type="submit" class="auth-primary-action" :disabled="isLoading">Log in</button>
      </form>
      <p v-if="authStore.registrationEnabled" class="mt-5 text-center text-sm text-ink-500">
        Don’t have an account?
        <router-link class="auth-link" :to="{ name: 'register' }"> Register </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
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

onMounted(() => authStore.loadPublicConfiguration().catch(() => {}));

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
