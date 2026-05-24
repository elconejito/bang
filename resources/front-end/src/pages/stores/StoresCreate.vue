<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'StoreIndex' }" class="hover:text-gray-700">All Stores</router-link>
      <span>›</span>
      <span class="text-gray-700">New Store</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Add a Store</h1>

    <div class="max-w-lg">
      <StoreForm @complete="onComplete" />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import StoreForm from '@/components/stores/StoreForm.vue'

const router = useRouter()

function onComplete() {
  router.push({ name: 'StoreIndex' })
}
</script>
