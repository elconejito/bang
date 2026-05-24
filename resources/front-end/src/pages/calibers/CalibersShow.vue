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
          <router-link
            :to="{ name: 'CalibersEdit', params: { caliber_id: caliberId } }"
            class="btn btn-outline-info"
          >
            <font-awesome-icon icon="edit" />
          </router-link>
        </h1>
        <p class="text-muted">{{ caliberTypeLabel }}</p>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link
          :to="{ name: 'AmmunitionCreate', params: { caliber_id: caliberId } }"
          class="btn btn-primary"
        >
          Add Ammunition
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

    <AmmunitionList :ammunition="ammunition.data" :caliber="caliber" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useLoading } from '@/composables/useLoading'
import AmmunitionList from '@/components/ammunition/AmmunitionList.vue'
import Loading from '@/components/Loading.vue'

const props = defineProps({
  caliberId: {
    type: Number,
    required: true,
  },
})

const calibersStore = useCalibersStore()
const ammunitionStore = useAmmunitionStore()
const { isLoading, loadingQueue } = useLoading()

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
</script>
