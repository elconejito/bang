<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <LoadingCard message="Loading Magazines..." />
    </div>
    <div v-else-if="hasError" class="flex justify-center py-12">
      <ErrorCard :error="error" />
    </div>
    <div v-else-if="showEmpty" class="flex justify-center py-12">
      <EmptyCard />
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <MagazineCard v-for="(magazine, i) in magazines" :key="i" :magazine="magazine" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import MagazineCard from '@/components/magazines/MagazineCard.vue'
import LoadingCard from '@/components/status/LoadingCard.vue'
import ErrorCard from '@/components/status/ErrorCard.vue'
import EmptyCard from '@/components/status/EmptyCard.vue'

const props = defineProps({
  magazines: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: [Error, Boolean],
    default: false,
  },
})

const hasError = computed(() => props.error !== false)
const showEmpty = computed(() => props.magazines.length === 0 && !props.isLoading && props.error === false)
</script>
