<template>
  <div></div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

onMounted(async () => {
  try {
    await authStore.logout()
  } finally {
    router.push({ name: 'login' })
  }
})
</script>
