<template>
  <div v-if="isLoading" class="flex h-screen items-center justify-center">
    <Loading class="text-3xl text-gray-400" />
  </div>
  <div v-else class="min-h-screen bg-canvas pb-[60px]">
    <TopNavigation />
    <router-view />
    <SiteFooter />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import TopNavigation from '@/components/navigation/TopNavigation.vue'
import SiteFooter from '@/components/SiteFooter.vue'
import Loading from '@/components/Loading.vue'
import { useLoading } from '@/composables/useLoading'
import { useReferenceStore } from '@/stores/reference'

const { isLoading, loadingQueue } = useLoading()
const referenceStore = useReferenceStore()

onMounted(async () => {
  isLoading.value = true
  loadingQueue.references = false
  try {
    await referenceStore.fetchAll()
  } catch (error) {
    console.error('Authenticated: failed to load references', error)
  } finally {
    loadingQueue.references = true
  }
})
</script>
