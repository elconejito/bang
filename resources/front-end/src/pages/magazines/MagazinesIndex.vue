<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Magazines</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Magazines</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link :to="{ name: 'MagazinesCreate' }" class="btn btn-outline-primary">
          <font-awesome-icon icon="plus-circle" /> Add Magazine
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
