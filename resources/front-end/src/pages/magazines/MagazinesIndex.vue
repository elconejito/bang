<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Magazines</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Magazines</h1>
      <router-link
        :to="{ name: 'MagazinesCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Magazine
      </router-link>
    </div>

    <MagazineList :magazines="magazines" :is-loading="isLoading" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useMagazinesStore } from '@/stores/magazines'
import { useLoading } from '@/composables/useLoading'
import MagazineList from '@/components/magazines/MagazineList.vue'

const magazinesStore = useMagazinesStore()
const { isLoading, loadingQueue } = useLoading()

const magazines = ref([])

onMounted(() => fetchMagazines())

async function fetchMagazines() {
  isLoading.value = true
  loadingQueue.magazines = false
  const { data } = await magazinesStore.fetchAll()
  magazines.value = data
  loadingQueue.magazines = true
}
</script>
