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

    <Modal modalId="create-caliber-form">
      <template #modalTitle>Add Caliber</template>
      <template #modalBody>
        <CaliberForm @complete="completeAddCaliber" />
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useLoading } from '@/composables/useLoading'
import { useModal } from '@/composables/useModal'
import CaliberList from '@/components/caliber/CaliberList.vue'
import CaliberForm from '@/components/caliber/CaliberForm.vue'
import Modal from '@/components/Modal.vue'

const calibersStore = useCalibersStore()
const { isLoading, loadingQueue } = useLoading()
const { closeModal } = useModal()

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

async function completeAddCaliber() {
  closeModal('create-caliber-form')
  await fetchData()
}
</script>
