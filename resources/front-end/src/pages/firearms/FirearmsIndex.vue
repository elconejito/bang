<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Firearms</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Firearms</h1>
      <router-link
        :to="{ name: 'FirearmsCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Firearm
      </router-link>
    </div>

    <FirearmList :firearms="firearms" :is-loading="isLoading" :error="error" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import { useLoading } from '@/composables/useLoading'
import FirearmList from '@/components/firearms/FirearmList.vue'

const firearmsStore = useFirearmsStore()
const { isLoading, loadingQueue } = useLoading()

const firearms = ref([])
const error = ref(false)

onMounted(() => fetchFirearms())

async function fetchFirearms() {
  isLoading.value = true
  loadingQueue.firearms = false
  error.value = false
  try {
    const { data } = await firearmsStore.fetchAll()
    firearms.value = data
  } catch (err) {
    error.value = err
  } finally {
    loadingQueue.firearms = true
  }
}
</script>
