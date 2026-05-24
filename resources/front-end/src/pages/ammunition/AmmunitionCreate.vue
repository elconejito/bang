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
      <span class="text-gray-700">Add Ammunition</span>
    </nav>

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Add Ammunition</h1>

    <div class="max-w-lg">
      <AmmunitionForm :caliber="caliber" @complete="onComplete" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCalibersStore } from '@/stores/calibers'
import { useLoading } from '@/composables/useLoading'
import Loading from '@/components/Loading.vue'
import AmmunitionForm from '@/components/ammunition/AmmunitionForm.vue'

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

function onComplete(created) {
  router.push({ name: 'AmmunitionShow', params: { caliber_id: props.caliberId, ammunition_id: created.id } })
}
</script>
