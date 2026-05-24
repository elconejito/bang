<template>
  <div v-if="isLoading" class="flex h-64 items-center justify-center">
    <Loading class="text-3xl text-gray-400" />
  </div>

  <div v-else class="container mx-auto px-4 py-6">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
      <router-link :to="{ name: 'dashboard' }" class="hover:text-gray-700">
        <font-awesome-icon icon="home" />
      </router-link>
      <span>›</span>
      <router-link :to="{ name: 'CalibersIndex' }" class="hover:text-gray-700">All Calibers</router-link>
      <span>›</span>
      <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliberId } }" class="hover:text-gray-700">{{ caliber.label }}</router-link>
      <span>›</span>
      <router-link
        :to="{ name: 'AmmunitionShow', params: { caliber_id: caliberId, ammunition_id: ammunitionId } }"
        class="hover:text-gray-700"
      >{{ ammunition.manufacturer }} {{ ammunition.label }}</router-link>
      <span>›</span>
      <span class="text-gray-700">Edit</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Ammunition</h1>

    <div class="max-w-lg">
      <EditAmmunitionForm :caliber="caliber" :original="ammunition" @complete="onComplete" />
    </div>
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
