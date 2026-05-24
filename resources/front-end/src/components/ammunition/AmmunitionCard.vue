<template>
  <div class="card ammunition-card">
    <div class="card-header">
      <h3 class="card-title">
        <small>{{ ammunition.manufacturer }}</small><br />
        <router-link
          :to="{
            name: 'AmmunitionShow',
            params: { caliber_id: caliber.id, ammunition_id: ammunition.id },
          }"
        >
          {{ ammunition.label }}
        </router-link>
      </h3>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span v-if="isLoading" class="number">
          <Loading />
        </span>
        <span v-else class="number" :title="formatQuantity(rounds)">
          {{ formatSmartQuantity(rounds) }}
        </span>
        <span class="label">RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span>{{ purposeLabel }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAmmunitionStore } from '@/stores/ammunition'
import { useNumbers } from '@/composables/useNumbers'
import { useLoading } from '@/composables/useLoading'
import Loading from '@/components/Loading.vue'

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

const ammunitionStore = useAmmunitionStore()
const { formatQuantity, formatSmartQuantity } = useNumbers()
const { isLoading, loadingQueue } = useLoading()

const rounds = ref(0)

const purposeLabel = computed(() => props.ammunition.purpose?.label ?? '')

onMounted(() => {
  isLoading.value = true
  loadingQueue.inventory = false
  ammunitionStore.fetchTotal(props.ammunition.id).then(({ data }) => {
    rounds.value = data.total
    loadingQueue.inventory = true
  })
})
</script>
