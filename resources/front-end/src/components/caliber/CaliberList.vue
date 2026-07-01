<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <LoadingCard message="Loading Calibers..." />
    </div>
    <div v-else-if="isEmpty">
      <EmptyCard
        title="No calibers yet"
        message="Add calibers so firearms, ammo, and accessories can be grouped consistently."
        action-label="Add Caliber"
        :action-to="{ name: 'CalibersCreate' }"
      />
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <CaliberCard v-for="(caliber, i) in calibers" :key="i" :caliber="caliber" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import CaliberCard from '@/components/caliber/CaliberCard.vue';
import LoadingCard from '@/components/status/LoadingCard.vue';
import EmptyCard from '@/components/status/EmptyCard.vue';

const props = defineProps({
  calibers: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
});

const isEmpty = computed(() => props.calibers.length === 0);
</script>
