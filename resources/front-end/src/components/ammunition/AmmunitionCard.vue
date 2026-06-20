<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <p class="text-xs text-gray-500">{{ ammunition.manufacturer }}</p>
      <h3 class="font-medium">
        <router-link
          :to="{ name: 'AmmoShow', params: { ammunition_id: ammunition.id } }"
          class="text-blue-600 hover:text-blue-700"
        >
          {{ ammunition.label }}
        </router-link>
      </h3>
    </div>

    <div class="flex items-center gap-4 bg-gray-50 px-4 py-3">
      <div class="text-center">
        <span v-if="isLoading" class="block text-2xl font-bold text-gray-400">
          <Loading />
        </span>
        <span v-else class="block text-2xl font-bold text-gray-800" :title="formatQuantity(rounds)">
          {{ formatSmartQuantity(rounds) }}
        </span>
        <span class="text-xs uppercase tracking-wide text-gray-500">Rnds</span>
      </div>
      <span v-if="purposeLabel" class="text-xs text-gray-500">{{ purposeLabel }}</span>
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
