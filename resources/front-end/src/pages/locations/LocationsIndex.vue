<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Locations</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Locations</h1>
      <router-link
        :to="{ name: 'LocationsCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Location
      </router-link>
    </div>

    <LocationList :locations="locations" :is-loading="isLoading" :error="error" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useLocationsStore } from '@/stores/locations'
import { useLoading } from '@/composables/useLoading'
import LocationList from '@/components/locations/LocationList.vue'

const locationsStore = useLocationsStore()
const { isLoading, loadingQueue } = useLoading()

const locations = ref([])
const error = ref(false)

onMounted(() => fetchLocations())

async function fetchLocations() {
  isLoading.value = true
  loadingQueue.locations = false
  error.value = false
  try {
    const { data } = await locationsStore.fetchAll()
    locations.value = data
  } catch (err) {
    error.value = err
  } finally {
    loadingQueue.locations = true
  }
}
</script>
