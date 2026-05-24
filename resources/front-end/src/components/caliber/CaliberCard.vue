<template>
  <div class="card caliber-card">
    <div class="card-header">
      <h3 class="card-title">
        <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }">
          {{ caliber.label }}
        </router-link>
      </h3>
      <h6 class="card-subtitle text-muted">{{ caliber.caliber_type.label }}</h6>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span class="number" :title="formatQuantity(totalSummary.total)">
          {{ formatSmartQuantity(totalSummary.total) }}
        </span>
        <span class="label">TOTAL RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span v-for="purpose in purposeTotals" :key="purpose">
          {{ getPurposeLabel(purpose) }}: <span :title="formatQuantity(totalSummary[purpose])">{{ formatSmartQuantity(totalSummary[purpose]) }}</span>
        </span>
      </div>
    </div>
    <div class="card-body">
      <h6>Used By:</h6>
      <p class="card-text">
        <span v-if="caliber.firearms.length === 0">None</span>
        <router-link
          class="badge bg-info text-dark me-2"
          v-for="(firearm, i) in caliber.firearms"
          :key="i"
          :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
        >
          {{ firearm.label }}
        </router-link>
      </p>
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
