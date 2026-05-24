<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">All Calibers</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Caliber</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          {{ caliber.label }}
          <button
            type="button"
            class="btn btn-outline-info"
            @click="openModal('edit-caliber-form')"
          >
            <font-awesome-icon icon="edit" />
          </button>
        </h1>
        <p class="text-muted">{{ caliberTypeLabel }}</p>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#create-ammunition-form"
        >
          Add Ammunition
        </button>
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

    <AmmunitionList :ammunition="ammunition.data" :caliber="caliber" />

    <Modal modalId="edit-caliber-form">
      <template #modalTitle>Edit Caliber Form</template>
      <template #modalBody>
        <EditCaliberForm :original="caliber" @complete="completeEditCaliber" />
      </template>
    </Modal>

    <Modal modalId="create-ammunition-form">
      <template #modalTitle>Add Ammunition Form</template>
      <template #modalBody>
        <AmmunitionForm :caliber="caliber" @complete="completeAddAmmunition" />
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useLoading } from '@/composables/useLoading'
import { useModal } from '@/composables/useModal'
import AmmunitionForm from '@/components/ammunition/AmmunitionForm.vue'
import AmmunitionList from '@/components/ammunition/AmmunitionList.vue'
import EditCaliberForm from '@/components/caliber/EditCaliberForm.vue'
import Loading from '@/components/Loading.vue'
import Modal from '@/components/Modal.vue'

const props = defineProps({
  caliberId: {
    type: Number,
    required: true,
  },
})

const calibersStore = useCalibersStore()
const ammunitionStore = useAmmunitionStore()
const { isLoading, loadingQueue } = useLoading()
const { openModal, closeModal } = useModal()

const caliber = ref({})
const ammunition = ref({ data: [], meta: {} })

const caliberTypeLabel = computed(() => caliber.value.caliber_type?.label ?? '')

onMounted(() => fetchData())

function fetchData() {
  isLoading.value = true
  loadingQueue.caliber = false
  loadingQueue.ammunition = false
  fetchCaliber()
  fetchAmmunition()
}

async function fetchCaliber() {
  const { data } = await calibersStore.fetchOne(props.caliberId)
  caliber.value = data
  loadingQueue.caliber = true
}

async function fetchAmmunition() {
  const response = await ammunitionStore.fetchAll(props.caliberId)
  ammunition.value.data = response.data
  ammunition.value.meta = response.meta ?? {}
  loadingQueue.ammunition = true
}

async function completeAddAmmunition() {
  closeModal('create-ammunition-form')
  loadingQueue.ammunition = false
  await fetchAmmunition()
}

async function completeEditCaliber() {
  closeModal('edit-caliber-form')
  loadingQueue.caliber = false
  await fetchCaliber()
}
</script>
