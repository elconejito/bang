<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Calibers</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Calibers</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link :to="{ name: 'CalibersCreate' }" class="btn btn-outline-primary">
          <font-awesome-icon icon="plus-circle" /> Add Caliber
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

    <CaliberList :calibers="calibers" :is-loading="isLoading" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useLoading } from '@/composables/useLoading'
import CaliberList from '@/components/caliber/CaliberList.vue'

const calibersStore = useCalibersStore()
const { isLoading, loadingQueue } = useLoading()

const calibers = ref([])

onMounted(() => fetchData())

async function fetchData() {
  isLoading.value = true
  loadingQueue.calibers = false
  try {
    const { data } = await calibersStore.fetchAll()
    calibers.value = data
  } finally {
    loadingQueue.calibers = true
  }
}
</script>
