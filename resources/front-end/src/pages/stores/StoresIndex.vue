<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Stores</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Stores</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link class="btn btn-outline-primary" :to="{ name: 'StoreCreate' }">
          <font-awesome-icon icon="plus-circle" /> Add Store
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
