<template>
  <nav class="sticky top-0 z-50 h-14 bg-ink-900 border-b border-black">
    <!-- Single flex row: logo → nav links → account (margin-left:auto) -->
    <div class="flex h-full items-center gap-7 px-8">

      <!-- Logo: circle mark + wordmark -->
      <router-link to="/" class="flex shrink-0 items-center gap-[9px]">
        <AppLogoMark />
        <span class="font-display text-[19px] font-extrabold leading-none tracking-[-0.02em] text-white">Bang</span>
      </router-link>

      <!-- Primary nav links -->
      <div class="flex h-full items-center gap-6">
        <template v-for="link in navLinks" :key="link.to">
          <router-link
            :to="link.to"
            class="flex h-full items-center text-[15px] font-medium text-ink-300 transition-colors hover:text-white"
            :class="isActive(link.to) ? 'text-white border-b-2 border-brass' : 'border-b-2 border-transparent'"
          >{{ link.label }}</router-link>
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
          <span class="flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-full bg-ink-700 font-mono text-xs font-semibold text-ink-300">
            {{ userInitial }}
          </span>
          <span>{{ userName }}</span>
          <ChevronDown class="h-[15px] w-[15px] shrink-0 text-muted transition-transform" :class="{ 'rotate-180': accountOpen }" />
        </button>

        <!-- Dropdown -->
        <div
          v-show="accountOpen"
          class="absolute right-0 top-full mt-1 w-52 rounded border border-line bg-surface shadow-lg"
          role="menu"
        >
          <ul class="py-1">
            <li role="menuitem">
              <div class="flex cursor-not-allowed items-center px-4 py-2 text-[14px] text-ink-400">
                Preferences
                <span class="ml-auto font-mono text-[10px] uppercase tracking-wider text-muted">Soon</span>
              </div>
            </li>
            <li class="my-1 border-t border-line" role="separator" />
            <li role="menuitem">
              <router-link
                :to="{ name: 'LocationIndex' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
              >Storage Locations</router-link>
            </li>
            <li role="menuitem">
              <router-link
                :to="{ name: 'RangesIndex' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
              >Ranges</router-link>
            </li>
            <li role="menuitem">
              <router-link
                :to="{ name: 'StoreIndex' }"
                class="block px-4 py-2 text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="accountOpen = false"
              >Stores</router-link>
            </li>
            <li class="my-1 border-t border-line" role="separator" />
            <li role="menuitem">
              <button
                class="w-full px-4 py-2 text-left text-[14px] text-ink-700 transition-colors hover:bg-ink-50"
                @click="handleLogout"
              >Logout</button>
            </li>
          </ul>
        </div>
      </div>

      <!-- Mobile hamburger -->
      <button
        class="ml-2 flex md:hidden items-center text-ink-300 transition-colors hover:text-white"
        @click="mobileOpen = !mobileOpen"
        aria-label="Toggle navigation"
      >
        <X v-if="mobileOpen" class="h-5 w-5" />
        <Menu v-else class="h-5 w-5" />
      </button>
    </div>

    <!-- Mobile menu -->
    <div v-show="mobileOpen" class="md:hidden border-t border-ink-800 bg-ink-900 px-4 pb-4 pt-2">
      <ul class="flex flex-col">
        <li v-for="link in navLinks" :key="link.to">
          <router-link
            :to="link.to"
            class="block rounded px-3 py-2.5 text-[15px] font-medium transition-colors"
            :class="isActive(link.to)
              ? 'bg-ink-800 text-white'
              : 'text-ink-300 hover:bg-ink-800 hover:text-white'"
            @click="mobileOpen = false"
          >{{ link.label }}</router-link>
        </li>
      </ul>
      <div class="mt-3 border-t border-ink-800 pt-3 text-[14px]">
        <router-link
          :to="{ name: 'LocationIndex' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
        >Storage Locations</router-link>
        <router-link
          :to="{ name: 'RangesIndex' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
        >Ranges</router-link>
        <router-link
          :to="{ name: 'StoreIndex' }"
          class="block rounded px-3 py-2 text-ink-400 transition-colors hover:text-white"
          @click="mobileOpen = false"
        >Stores</router-link>
        <div class="mt-2 border-t border-ink-800 pt-2">
          <button
            class="block w-full rounded px-3 py-2 text-left text-ink-400 transition-colors hover:text-white"
            @click="handleLogout"
          >Logout</button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ChevronDown, Menu, X } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import AppLogoMark from '@/components/AppLogoMark.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const userName = computed(() => authStore.currentUser?.name ?? '')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || '?')

const accountOpen = ref(false)
const mobileOpen = ref(false)
const accountRef = ref(null)

const navLinks = [
  { to: '/firearms',    label: 'Firearms'    },
  { to: '/ammo',        label: 'Ammo'        },
  { to: '/accessories', label: 'Accessories' },
  { to: '/training',    label: 'Training'    },
]

function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/')
}

function handleOutsideClick(e) {
  if (accountRef.value && !accountRef.value.contains(e.target)) {
    accountOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleOutsideClick))
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))

async function handleLogout() {
  accountOpen.value = false
  mobileOpen.value = false
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>
