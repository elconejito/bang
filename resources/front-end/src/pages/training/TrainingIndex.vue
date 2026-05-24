<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Training</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Training</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link class="btn btn-outline-primary" :to="{ name: 'TrainingCreate' }">
          <font-awesome-icon icon="plus-circle" /> Add Training
        </router-link>
        <div class="btn-group" role="group" aria-label="View Options">
          <button type="button" class="btn btn-outline-dark">
            <font-awesome-icon icon="sort" />
          </button>
          <button type="button" class="btn btn-outline-dark">
            <font-awesome-icon icon="sliders-h" />
          </button>
        </div>
      </div>
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
