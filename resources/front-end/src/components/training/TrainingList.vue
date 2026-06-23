<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <LoadingCard message="Loading Training..." />
    </div>
    <div v-else-if="hasError" class="flex justify-center py-12">
      <ErrorCard :error="error" />
    </div>
    <div v-else-if="showEmpty">
      <EmptyCard
        title="No training sessions yet"
        message="Log a session to apply rounds, ammo usage, and suppressor counts automatically."
        action-label="Log Session"
        :action-to="{ name: 'TrainingCreate' }"
      />
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <TrainingCard v-for="(t, i) in training" :key="i" :training="t" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import TrainingCard from '@/components/training/TrainingCard.vue'
import LoadingCard from '@/components/status/LoadingCard.vue'
import ErrorCard from '@/components/status/ErrorCard.vue'
import EmptyCard from '@/components/status/EmptyCard.vue'

const props = defineProps({
  training: {
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
const showEmpty = computed(() => props.training.length === 0 && !props.isLoading && props.error === false)
</script>
