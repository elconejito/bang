<template>
  <div>
    <h3 class="mb-4 text-base font-semibold text-gray-900">Firearms</h3>
    <FirearmList :firearms="firearms" :is-loading="isLoading" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import { useLoading } from '@/composables/useLoading'
import FirearmList from '@/components/firearms/FirearmList.vue'

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
  caliber: {
    type: Object,
    required: true,
  },
})

const firearmsStore = useFirearmsStore()
const { isLoading, loadingQueue } = useLoading()

const firearms = ref([])

onMounted(() => fetchFirearms())

async function fetchFirearms() {
  isLoading.value = true
  loadingQueue.firearms = false
  try {
    const { data } = await firearmsStore.fetchAll({ search: `calibers.id:${props.caliber.id}` })
    firearms.value = data
  } finally {
    loadingQueue.firearms = true
  }
}
</script>
