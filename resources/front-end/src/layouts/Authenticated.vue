<template>
  <div v-if="isLoading">Loading the application</div>
  <div v-else>
    <TopNavigation />
    <router-view />
    <SiteFooter />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import TopNavigation from '@/components/navigation/TopNavigation.vue'
import SiteFooter from '@/components/SiteFooter.vue'
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

<style></style>
