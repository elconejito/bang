<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <h3 class="font-medium">
        <router-link
          :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
          class="text-blue-600 hover:text-blue-700"
        >
          {{ caliber.label }}
        </router-link>
      </h3>
      <p class="mt-0.5 text-xs text-gray-500">{{ caliber.caliber_type.label }}</p>
    </div>

    <div class="flex items-start gap-4 border-b border-gray-100 bg-gray-50 px-4 py-3">
      <div class="text-center">
        <span
          class="block text-2xl font-bold text-gray-800"
          :title="formatQuantity(totalSummary.total)"
        >
          {{ formatSmartQuantity(totalSummary.total) }}
        </span>
        <span class="text-xs uppercase tracking-wide text-gray-500">Total Rnds</span>
      </div>
      <div class="flex flex-col gap-0.5 text-xs text-gray-600">
        <span v-for="purpose in purposeTotals" :key="purpose">
          {{ getPurposeLabel(purpose) }}:
          <span
            class="font-medium"
            :title="formatQuantity(totalSummary[purpose])"
          >{{ formatSmartQuantity(totalSummary[purpose]) }}</span>
        </span>
      </div>
    </div>

    <div class="px-4 py-3">
      <p class="mb-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Used By</p>
      <div v-if="caliber.firearms.length === 0" class="text-sm text-gray-400">None</div>
      <div v-else class="flex flex-wrap gap-1">
        <router-link
          v-for="(firearm, i) in caliber.firearms"
          :key="i"
          :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
          class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800 hover:bg-blue-200 transition-colors"
        >
          {{ firearm.label }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useNumbers } from '@/composables/useNumbers'
import { usePurpose } from '@/composables/usePurpose'
import { useLoading } from '@/composables/useLoading'

const props = defineProps({
  caliber: {
    type: Object,
    required: true,
  },
})

const calibersStore = useCalibersStore()
const { formatQuantity, formatSmartQuantity } = useNumbers()
const { getPurposeLabel } = usePurpose()
const { isLoading, loadingQueue } = useLoading()

const totalSummary = ref({})

const purposeTotals = computed(() => Object.keys(totalSummary.value).filter((p) => p !== 'total'))

onMounted(() => {
  isLoading.value = true
  loadingQueue.caliber = false
  calibersStore.fetchTotal(props.caliber.id).then(({ data }) => {
    totalSummary.value = data
    loadingQueue.caliber = true
  })
})
</script>
