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
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Edit Caliber</h1>
      </div>
    </div>

    <EditCaliberForm :original="caliber" @complete="onComplete" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCalibersStore } from '@/stores/calibers'
import { useLoading } from '@/composables/useLoading'
import Loading from '@/components/Loading.vue'
import EditCaliberForm from '@/components/caliber/EditCaliberForm.vue'

const props = defineProps({
  caliberId: { type: Number, required: true },
})

const router = useRouter()
const calibersStore = useCalibersStore()
const { isLoading, loadingQueue } = useLoading()

const caliber = ref({})

onMounted(async () => {
  isLoading.value = true
  loadingQueue.caliber = false
  const { data } = await calibersStore.fetchOne(props.caliberId)
  caliber.value = data
  loadingQueue.caliber = true
})

function onComplete() {
  router.push({ name: 'CalibersShow', params: { caliber_id: props.caliberId } })
}
</script>
