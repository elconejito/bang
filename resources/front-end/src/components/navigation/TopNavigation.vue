<template>
  <nav class="sticky top-0 z-50 h-14 bg-ink-900 border-b border-black">
    <!-- Single flex row: logo → nav links → account (margin-left:auto) -->
    <div class="flex h-full items-center gap-2 px-4 sm:gap-5 sm:px-6 lg:gap-7 lg:px-8">
      <!-- Logo: circle mark + wordmark -->
      <router-link to="/" class="flex shrink-0 items-center gap-[9px]">
        <AppLogoMark />
        <span
          class="font-display text-[19px] font-extrabold leading-none tracking-[-0.02em] text-white"
          >Bang</span
        >
      </router-link>

      <!-- Primary nav links -->
      <div data-testid="primary-navigation" class="hidden h-full items-center gap-6 md:flex">
        <template v-for="link in navLinks" :key="link.to">
          <router-link
            :to="link.to"
            class="flex h-full items-center text-[15px] font-medium text-ink-300 transition-colors hover:text-white"
            :class="
              isActive(link.to)
                ? 'text-white border-b-2 border-brass'
                : 'border-b-2 border-transparent'
            "
            >{{ link.label }}</router-link
          >
        </template>
      </div>

      <!-- Account: pushed to far right -->
      <div class="relative ml-auto flex items-center" ref="accountRef">
        <button
          class="flex cursor-pointer items-center gap-2 text-[14px] text-ink-300 transition-colors hover:text-white"
          @click="accountOpen = !accountOpen"
          aria-haspopup="true"
          :aria-expanded="accountOpen"
        >
          <span
            class="flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-full bg-ink-700 font-mono text-xs font-semibold text-ink-300"
          >
            {{ userInitial }}
          </span>
          <span data-testid="account-name" class="hidden sm:inline">{{ userName }}</span>
          <ChevronDown
            class="hidden h-[15px] w-[15px] shrink-0 text-muted transition-transform sm:block"
            :class="{ 'rotate-180': accountOpen }"
          />
        </button>

        <!-- Dropdown -->
        <div
          v-show="accountOpen"
          class="absolute right-0 top-full mt-1 w-52 rounded border border-line bg-surface shadow-lg"
          role="menu"
        >
          <ul class="py-1">
            <li role="menuitem">
              <router-link
                :to="{ name: 'Preferences' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
                >Preferences</router-link
              >
            </li>
            <li role="menuitem">
              <router-link
                :to="{ name: 'PictureLibrary' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
                >Photo Library</router-link
              >
            </li>
            <li class="my-1 border-t border-line" role="separator" />
            <li role="menuitem">
              <router-link
                :to="{ name: 'ReferenceData' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
                >Manage Lists</router-link
              >
            </li>
            <li class="my-1 border-t border-line" role="separator" />
            <li role="menuitem">
              <button
                class="w-full px-4 py-2 text-left text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="handleLogout"
              >
                Logout
              </button>
            </li>
          </ul>
        </div>
      </div>

      <!-- Mobile hamburger -->
      <button
        class="ml-1 flex items-center text-ink-300 transition-colors hover:text-white md:hidden"
        @click="mobileOpen = !mobileOpen"
        aria-label="Toggle navigation"
        aria-controls="mobile-navigation"
        :aria-expanded="mobileOpen"
      >
        <X v-if="mobileOpen" class="h-5 w-5" />
        <Menu v-else class="h-5 w-5" />
      </button>
    </div>

    <!-- Mobile menu -->
    <div
      v-show="mobileOpen"
      id="mobile-navigation"
      data-testid="mobile-navigation"
      class="border-t border-ink-800 bg-ink-900 px-4 pb-4 pt-2 md:hidden"
    >
      <ul class="flex flex-col">
        <li v-for="link in navLinks" :key="link.to">
          <router-link
            :to="link.to"
            class="block rounded px-3 py-2.5 text-[15px] font-medium transition-colors"
            :class="
              isActive(link.to)
                ? 'bg-ink-800 text-white'
                : 'text-ink-300 hover:bg-ink-800 hover:text-white'
            "
            @click="mobileOpen = false"
            >{{ link.label }}</router-link
          >
        </li>
      </ul>
      <div class="mt-3 border-t border-ink-800 pt-3 text-[14px]">
        <router-link
          :to="{ name: 'Preferences' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
          >Preferences</router-link
        >
        <router-link
          :to="{ name: 'ReferenceData' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
          >Manage Lists</router-link
        >
        <router-link
          :to="{ name: 'PictureLibrary' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
          >Photo Library</router-link
        >
        <div class="mt-2 border-t border-ink-800 pt-2">
          <button
            class="block w-full rounded px-3 py-2 text-left text-ink-400 transition-colors hover:text-white"
            @click="handleLogout"
          >
            Logout
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ChevronDown, Menu, X } from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth';
import AppLogoMark from '@/components/AppLogoMark.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const userName = computed(() => authStore.currentUser?.name ?? '');
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || '?');

const accountOpen = ref(false);
const mobileOpen = ref(false);
const accountRef = ref(null);

const navLinks = [
  { to: '/firearms', label: 'Firearms' },
  { to: '/ammo', label: 'Ammo' },
  { to: '/accessories', label: 'Accessories' },
  { to: '/training', label: 'Training' },
];

function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/');
}

function handleOutsideClick(e) {
  if (accountRef.value && !accountRef.value.contains(e.target)) {
    accountOpen.value = false;
  }
}

onMounted(() => document.addEventListener('click', handleOutsideClick));
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick));

async function handleLogout() {
  accountOpen.value = false;
  mobileOpen.value = false;
  await authStore.logout();
  router.push({ name: 'login' });
}
</script>
