<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }"><font-awesome-icon icon="home" /></router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">All Calibers</router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliberId } }">{{ caliber.label }}</router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'AmmunitionShow', params: { caliber_id: caliberId, ammunition_id: ammunitionId } }">
            {{ ammunition.manufacturer }} {{ ammunition.label }}
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Edit Ammunition</h1>
      </div>
    </div>

    <EditAmmunitionForm :caliber="caliber" :original="ammunition" @complete="onComplete" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCalibersStore } from '@/stores/calibers'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useLoading } from '@/composables/useLoading'
import Loading from '@/components/Loading.vue'
import EditAmmunitionForm from '@/components/ammunition/EditAmmunitionForm.vue'

const props = defineProps({
  caliberId: { type: Number, required: true },
  ammunitionId: { type: Number, required: true },
})

const router = useRouter()
const calibersStore = useCalibersStore()
const ammunitionStore = useAmmunitionStore()
const { isLoading, loadingQueue } = useLoading()

const caliber = ref({})
const ammunition = ref({})

onMounted(async () => {
  isLoading.value = true
  loadingQueue.caliber = false
  loadingQueue.ammunition = false
  const [caliberRes, ammoRes] = await Promise.all([
    calibersStore.fetchOne(props.caliberId),
    ammunitionStore.fetchOne(props.caliberId, props.ammunitionId),
  ])
  caliber.value = caliberRes.data
  ammunition.value = ammoRes.data
  loadingQueue.caliber = true
  loadingQueue.ammunition = true
})

function onComplete() {
  router.push({ name: 'AmmunitionShow', params: { caliber_id: props.caliberId, ammunition_id: props.ammunitionId } })
}
</script>
