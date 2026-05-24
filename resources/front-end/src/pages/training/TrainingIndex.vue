<template>
  <div class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <span class="text-gray-700">All Training</span>
    </nav>

    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Training</h1>
      <router-link
        :to="{ name: 'TrainingCreate' }"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      >
        <font-awesome-icon icon="plus-circle" /> Add Training
      </router-link>
    </div>

    <TrainingList :training="training" :is-loading="isLoading" :error="error" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTrainingStore } from '@/stores/training'
import { useLoading } from '@/composables/useLoading'
import TrainingList from '@/components/training/TrainingList.vue'

const trainingStore = useTrainingStore()
const { isLoading, loadingQueue } = useLoading()

const training = ref([])
const error = ref(false)

onMounted(() => fetchTraining())

async function fetchTraining() {
  isLoading.value = true
  loadingQueue.training = false
  error.value = false
  try {
    const { data } = await trainingStore.fetchAll()
    training.value = data
  } catch (err) {
    error.value = err
  } finally {
    loadingQueue.training = true
  }
}
</script>
