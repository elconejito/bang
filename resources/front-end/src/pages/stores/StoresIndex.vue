<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Stores</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Stores</h1>
      <router-link
        :to="{ name: 'StoreCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Store
      </router-link>
    </div>

    <StoreList :stores="stores" :is-loading="isLoading" :error="error" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useGunStoresStore } from '@/stores/gunStores'
import { useLoading } from '@/composables/useLoading'
import StoreList from '@/components/stores/StoreList.vue'

const gunStoresStore = useGunStoresStore()
const { isLoading, loadingQueue } = useLoading()

const stores = ref([])
const error = ref(false)

onMounted(() => fetchStores())

async function fetchStores() {
  isLoading.value = true
  loadingQueue.stores = false
  error.value = false
  try {
    const { data } = await gunStoresStore.fetchAll()
    stores.value = data
  } catch (err) {
    error.value = err
  } finally {
    loadingQueue.stores = true
  }
}
</script>
