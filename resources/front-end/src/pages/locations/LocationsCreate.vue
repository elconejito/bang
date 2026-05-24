<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'LocationIndex' }" class="hover:text-gray-700">All Locations</router-link>
      <span>›</span>
      <span class="text-gray-700">Add Location</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Add Location</h1>

    <div class="max-w-lg">
      <LocationForm @complete="onComplete" />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import LocationForm from '@/components/locations/LocationForm.vue'

const router = useRouter()

function onComplete() {
  router.push({ name: 'LocationIndex' })
}
</script>
