<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Locations</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Locations</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link class="btn btn-outline-primary" :to="{ name: 'LocationsCreate' }">
          <font-awesome-icon icon="plus-circle" /> Add Location
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
