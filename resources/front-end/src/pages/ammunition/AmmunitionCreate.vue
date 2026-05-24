<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav class="has-breadcrumbs" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">
            All Calibers
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }">
            {{ caliber.label }}
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Ammunition</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>{{ ammunition.manufacturer }}</small><br />
          {{ ammunition.label }}
          <button
            type="button"
            class="btn btn-outline-info"
            @click="openModal('edit-ammunition-form')"
          >
            <font-awesome-icon icon="edit" />
          </button>
        </h1>
      </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" v-for="(tab, i) in tabNames" :key="i">
        <span
          class="btn btn-link nav-link"
          :class="{ active: tabNameSlug(tab) === currentTab }"
          @click="setCurrentTab(tabNameSlug(tab))"
        >
          {{ tab }}
        </span>
      </li>
    </ul>

    <div class="tab-content py-3">
      <component :is="currentTabComponent" :ammunition="ammunition" :caliber="caliber" />
    </div>

    <Modal modalId="edit-ammunition-form">
      <template #modalTitle>Edit Ammunition Form</template>
      <template #modalBody>
        <EditAmmunitionForm
          :caliber="caliber"
          :original="ammunition"
          @complete="completeEditAmmunition"
        />
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useLoading } from '@/composables/useLoading'
import { useModal } from '@/composables/useModal'
import { useNavTabs } from '@/composables/useNavTabs'
import Loading from '@/components/Loading.vue'
import Modal from '@/components/Modal.vue'
import EditAmmunitionForm from '@/components/ammunition/EditAmmunitionForm.vue'
import AmmunitionDetails from '@/components/ammunition/AmmunitionDetails.vue'
import AmmunitionInventory from '@/components/ammunition/AmmunitionInventory.vue'
import AmmunitionTraining from '@/components/ammunition/AmmunitionTraining.vue'
import AmmunitionFirearms from '@/components/ammunition/AmmunitionFirearms.vue'
import AmmunitionImages from '@/components/ammunition/AmmunitionImages.vue'

const props = defineProps({
  ammunitionId: {
    type: Number,
    required: true,
  },
  caliberId: {
    type: Number,
    required: true,
  },
})

const calibersStore = useCalibersStore()
const ammunitionStore = useAmmunitionStore()
const { isLoading, loadingQueue } = useLoading()
const { openModal, closeModal } = useModal()
const { tabs, currentTab, currentTabComponent, tabNames, initTabs, setCurrentTab, tabNameSlug } = useNavTabs()

const caliber = ref({})
const ammunition = ref({})

onMounted(() => {
  initTabs({
    details:   { active: true,  label: 'Details',   component: AmmunitionDetails },
    inventory: { active: false, label: 'Inventory', component: AmmunitionInventory },
    training:  { active: false, label: 'Training',  component: AmmunitionTraining },
    firearms:  { active: false, label: 'Firearms',  component: AmmunitionFirearms },
    images:    { active: false, label: 'Images',    component: AmmunitionImages },
  })
  fetchData()
})

function fetchData() {
  isLoading.value = true
  loadingQueue.caliber = false
  loadingQueue.ammunition = false
  fetchCaliber()
  fetchAmmunition()
}

async function fetchCaliber() {
  try {
    const { data } = await calibersStore.fetchOne(props.caliberId)
    caliber.value = data
  } finally {
    loadingQueue.caliber = true
  }
}

async function fetchAmmunition() {
  try {
    const { data } = await ammunitionStore.fetchOne(props.caliberId, props.ammunitionId)
    ammunition.value = data
  } finally {
    loadingQueue.ammunition = true
  }
}

async function completeEditAmmunition() {
  closeModal('edit-ammunition-form')
  loadingQueue.ammunition = false
  await fetchAmmunition()
}
</script>
