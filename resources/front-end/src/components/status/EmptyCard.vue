<template>
  <EmptyState
    :title="title"
    :message="message"
    :action-label="actionLabel"
    :action-to="actionTo"
  />
</template>

<script setup>
import EmptyState from '@/components/EmptyState.vue'

defineProps({
  title: { type: String, default: 'Nothing found' },
  message: { type: String, default: 'There are no records to show yet.' },
  actionLabel: { type: String, default: '' },
  actionTo: { type: [String, Object], default: null },
})
</script>
