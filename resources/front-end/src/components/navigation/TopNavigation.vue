<template>
  <nav class="sticky top-0 z-50 bg-gray-900 shadow-lg">
    <div class="container mx-auto flex h-14 items-center justify-between px-4">

      <router-link to="/" class="text-xl font-bold text-white hover:text-gray-200">Bang</router-link>

      <!-- Mobile toggle -->
      <button
        class="rounded p-2 text-gray-300 hover:bg-gray-700 hover:text-white md:hidden"
        @click="mobileOpen = !mobileOpen"
        aria-label="Toggle navigation"
      >
        <font-awesome-icon icon="bars" />
      </button>

      <!-- Nav body — hidden on mobile unless open, always flex on md+ -->
      <div
        class="md:static md:flex md:w-auto md:flex-1 md:items-center md:justify-between md:p-0"
        :class="mobileOpen
          ? 'absolute left-0 top-14 z-40 flex w-full flex-col bg-gray-900 px-4 pb-4'
          : 'hidden'"
      >
        <ul class="flex flex-col gap-1 md:flex-row">
          <li v-for="link in navLinks" :key="JSON.stringify(link.to)">
            <router-link
              :to="link.to"
              class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
              active-class="bg-gray-700 !text-white"
              @click="mobileOpen = false"
            >{{ link.label }}</router-link>
          </li>
        </ul>

        <!-- Authenticated: username + dropdown -->
        <div v-if="isAuthenticated" class="mt-3 flex items-center gap-3 md:mt-0">
          <span class="text-sm text-gray-300">{{ userName }}</span>
          <div class="relative" ref="dropdownRef">
            <button
              class="rounded p-2 text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
              @click="menuOpen = !menuOpen"
              aria-label="User menu"
            >
              <font-awesome-icon icon="bars" />
            </button>
            <ul
              v-show="menuOpen"
              class="absolute right-0 top-full mt-1 min-w-[120px] rounded border border-gray-700 bg-gray-800 shadow-lg"
            >
              <li>
                <button
                  class="w-full px-4 py-2 text-left text-sm text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
                  @click="callLogout"
                >Logout</button>
              </li>
            </ul>
          </div>
        </div>

        <!-- Unauthenticated: login / register -->
        <div v-else class="mt-3 flex items-center gap-2 md:mt-0">
          <router-link
            :to="{ name: 'login' }"
            class="rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
            active-class="bg-gray-700 !text-white"
            @click="mobileOpen = false"
          >Login</router-link>
          <router-link
            :to="{ name: 'register' }"
            class="rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
            active-class="bg-gray-700 !text-white"
            @click="mobileOpen = false"
          >Register</router-link>
        </div>
      </div>

    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const userName = computed(() => authStore.currentUser?.name ?? '-')

const mobileOpen = ref(false)
const menuOpen = ref(false)
const dropdownRef = ref(null)

const navLinks = [
  { to: '/calibers',              label: 'Calibers'  },
  { to: '/firearms',              label: 'Firearms'  },
  { to: '/magazines',             label: 'Magazines' },
  { to: '/training',              label: 'Training'  },
  { to: { name: 'StoreIndex' },   label: 'Stores'    },
  { to: { name: 'LocationIndex'}, label: 'Locations' },
]

function handleOutsideClick(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    menuOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleOutsideClick))
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick))

async function callLogout() {
  menuOpen.value = false
  mobileOpen.value = false
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>
