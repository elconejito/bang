<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <h3 class="font-medium">
        <router-link
          :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
          class="text-blue-600 hover:text-blue-700"
        >
          {{ firearm.label }}
        </router-link>
      </h3>
    </div>

    <div class="px-4 py-3">
      <p class="mb-3 text-sm">
        <span class="text-gray-500">{{ firearm.manufacturer }}</span><br />
        <span class="font-medium">{{ firearm.model }}</span>
      </p>

      <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">Calibers Supported</p>
      <div v-if="firearm.calibers.length === 0" class="text-sm text-gray-400">None</div>
      <div v-else class="flex flex-wrap gap-1">
        <router-link
          v-for="(caliber, i) in firearm.calibers"
          :key="i"
          :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
          class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800 transition-colors hover:bg-blue-200"
        >
          {{ caliber.label }}
        </router-link>
      </div>
    </div>

    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3 text-center">
      <span class="block text-2xl font-bold text-gray-800" :title="formatQuantity(5000)">
        {{ formatSmartQuantity(5000) }}
      </span>
      <span class="text-xs uppercase tracking-wide text-gray-500">Rnds Fired</span>
    </div>
  </div>
</template>

<script setup>
import { useNumbers } from '@/composables/useNumbers'

defineProps({
  firearm: {
    type: Object,
    required: true,
  },
})

const { formatQuantity, formatSmartQuantity } = useNumbers()
</script>
