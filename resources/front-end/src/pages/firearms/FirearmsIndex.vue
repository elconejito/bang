<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Firearms</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Firearms</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link :to="{ name: 'FirearmsCreate' }" class="btn btn-outline-primary">
          <font-awesome-icon icon="plus-circle" /> Add Firearm
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
