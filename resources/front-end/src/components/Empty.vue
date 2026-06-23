<template>
  <EmptyState :title="message" />
</template>

<script setup>
import EmptyState from '@/components/EmptyState.vue'

defineProps({
  message: { type: String, required: true },
})
</script>
