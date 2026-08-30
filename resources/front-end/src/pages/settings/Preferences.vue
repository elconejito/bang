<template>
  <div class="mx-auto max-w-[960px] px-4 py-6 pb-16 sm:px-6 lg:px-8">
    <AppBreadcrumb :crumbs="[{ label: 'Account' }, { label: 'Preferences' }]" class="mb-4" />
    <div class="mb-6">
      <h1 class="font-display text-[28px] font-bold tracking-[-0.02em] text-ink-900">
        Preferences
      </h1>
      <p class="mt-1 text-[15px] text-muted">Manage the information you use to sign in to Bang.</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
      <section
        class="rounded border border-line bg-surface p-5 sm:p-6"
        aria-labelledby="profile-heading"
      >
        <div class="mb-5 flex items-start gap-3">
          <div class="rounded bg-ink-50 p-2 text-brass-800"><UserRound class="h-5 w-5" /></div>
          <div>
            <h2 id="profile-heading" class="text-[17px] font-semibold text-ink-900">
              Profile information
            </h2>
            <p class="mt-1 text-sm text-muted">Your name and email address.</p>
          </div>
        </div>

        <div
          v-if="profileSuccess"
          class="mb-4 rounded border border-success-border bg-success-bg p-3 text-sm text-success"
          role="status"
        >
          {{ profileSuccess }}
        </div>
        <FormError v-if="profileError" :error="profileError" class="mb-4" />

        <form class="space-y-4" @submit.prevent="saveProfile">
          <div>
            <label for="name" class="mb-1 block text-sm font-medium text-ink-700">Name</label>
            <input
              id="name"
              v-model="profile.name"
              autocomplete="name"
              required
              class="w-full rounded border border-line bg-white px-3 py-2 text-sm text-ink-900 outline-none transition-colors placeholder:text-muted focus:border-brass focus:ring-2 focus:ring-brass/20"
            />
          </div>
          <div>
            <label for="email" class="mb-1 block text-sm font-medium text-ink-700"
              >Email address</label
            >
            <input
              id="email"
              v-model="profile.email"
              type="email"
              autocomplete="email"
              required
              class="w-full rounded border border-line bg-white px-3 py-2 text-sm text-ink-900 outline-none transition-colors placeholder:text-muted focus:border-brass focus:ring-2 focus:ring-brass/20"
            />
          </div>
          <button
            type="submit"
            :disabled="profileLoading"
            class="inline-flex min-h-10 items-center justify-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <LoaderCircle v-if="profileLoading" class="h-4 w-4 animate-spin" />
            {{ profileLoading ? 'Saving…' : 'Save profile' }}
          </button>
        </form>
      </section>

      <section
        class="rounded border border-line bg-surface p-5 sm:p-6"
        aria-labelledby="password-heading"
      >
        <div class="mb-5 flex items-start gap-3">
          <div class="rounded bg-ink-50 p-2 text-brass-800"><KeyRound class="h-5 w-5" /></div>
          <div>
            <h2 id="password-heading" class="text-[17px] font-semibold text-ink-900">Password</h2>
            <p class="mt-1 text-sm text-muted">Use a new, secure password for your account.</p>
          </div>
        </div>

        <div
          v-if="passwordSuccess"
          class="mb-4 rounded border border-success-border bg-success-bg p-3 text-sm text-success"
          role="status"
        >
          {{ passwordSuccess }}
        </div>
        <FormError v-if="passwordError" :error="passwordError" class="mb-4" />

        <form class="space-y-4" @submit.prevent="savePassword">
          <div>
            <label for="current_password" class="mb-1 block text-sm font-medium text-ink-700">
              Current password
            </label>
            <input
              id="current_password"
              v-model="password.current_password"
              type="password"
              autocomplete="current-password"
              required
              class="w-full rounded border border-line bg-white px-3 py-2 text-sm text-ink-900 outline-none transition-colors placeholder:text-muted focus:border-brass focus:ring-2 focus:ring-brass/20"
            />
          </div>
          <div>
            <label for="new_password" class="mb-1 block text-sm font-medium text-ink-700">
              New password
            </label>
            <input
              id="new_password"
              v-model="password.password"
              type="password"
              autocomplete="new-password"
              required
              class="w-full rounded border border-line bg-white px-3 py-2 text-sm text-ink-900 outline-none transition-colors placeholder:text-muted focus:border-brass focus:ring-2 focus:ring-brass/20"
            />
          </div>
          <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-ink-700">
              Confirm new password
            </label>
            <input
              id="password_confirmation"
              v-model="password.password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              class="w-full rounded border border-line bg-white px-3 py-2 text-sm text-ink-900 outline-none transition-colors placeholder:text-muted focus:border-brass focus:ring-2 focus:ring-brass/20"
            />
          </div>
          <button
            type="submit"
            :disabled="passwordLoading"
            class="inline-flex min-h-10 items-center justify-center gap-2 rounded border border-[#b08a2e] bg-brass px-4 py-2 text-sm font-semibold text-ink-900 transition-colors hover:bg-brass-600 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <LoaderCircle v-if="passwordLoading" class="h-4 w-4 animate-spin" />
            {{ passwordLoading ? 'Updating…' : 'Update password' }}
          </button>
        </form>
      </section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { KeyRound, LoaderCircle, UserRound } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import FormError from '@/components/FormError.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const profile = reactive({ name: '', email: '' });
const password = reactive({ current_password: '', password: '', password_confirmation: '' });
const profileLoading = ref(false);
const passwordLoading = ref(false);
const profileError = ref(null);
const passwordError = ref(null);
const profileSuccess = ref('');
const passwordSuccess = ref('');

function syncProfile(user) {
  profile.name = user?.name ?? '';
  profile.email = user?.email ?? '';
}

watch(() => authStore.currentUser, syncProfile, { immediate: true });

function setError(target, exception) {
  if (exception.response?.data?.errors) {
    exception.errorBag = exception.response.data.errors;
  }
  target.value = exception;
}

async function saveProfile() {
  profileLoading.value = true;
  profileError.value = null;
  profileSuccess.value = '';

  try {
    const response = await authStore.updateProfile({ ...profile });
    profileSuccess.value = response.message;
  } catch (exception) {
    setError(profileError, exception);
  } finally {
    profileLoading.value = false;
  }
}

async function savePassword() {
  passwordLoading.value = true;
  passwordError.value = null;
  passwordSuccess.value = '';

  try {
    const response = await authStore.updatePassword({ ...password });
    password.current_password = '';
    password.password = '';
    password.password_confirmation = '';
    passwordSuccess.value = response.message;
  } catch (exception) {
    setError(passwordError, exception);
  } finally {
    passwordLoading.value = false;
  }
}
</script>
