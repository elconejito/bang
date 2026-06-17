<template>
  <div>
    <div v-if="isLoading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="n in 6" :key="n" class="h-[320px] animate-pulse rounded border border-line bg-ink-50" />
    </div>

    <div v-else-if="firearms.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
      <p class="font-display text-[17px] font-semibold text-ink-700">No firearms found</p>
      <p class="mt-1 text-[14px] text-muted">Try adjusting your search or filters</p>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <FirearmCard v-for="firearm in firearms" :key="firearm.id" :firearm="firearm" />
    </div>
  </div>
</template>

<script setup>
import FirearmCard from '@/components/firearms/FirearmCard.vue'

defineProps({
  firearms: { type: Array, required: true },
  isLoading: { type: Boolean, default: false },
})
</script>
