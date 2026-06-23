<template>
  <div>
    <div v-if="isLoading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="n in 6" :key="n" class="h-[320px] animate-pulse rounded border border-line bg-ink-50" />
    </div>

    <EmptyState
      v-else-if="firearms.length === 0"
      :title="emptyTitle"
      :message="emptyMessage"
      :action-label="emptyActionLabel"
      :action-to="emptyActionTo"
    />

    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <FirearmCard v-for="firearm in firearms" :key="firearm.id" :firearm="firearm" />
    </div>
  </div>
</template>

<script setup>
import FirearmCard from '@/components/firearms/FirearmCard.vue'
import EmptyState from '@/components/EmptyState.vue'

defineProps({
  firearms: { type: Array, required: true },
  isLoading: { type: Boolean, default: false },
  emptyTitle: { type: String, default: 'No firearms found' },
  emptyMessage: { type: String, default: 'Try adjusting your search or filters.' },
  emptyActionLabel: { type: String, default: '' },
  emptyActionTo: { type: [String, Object], default: null },
})
</script>
