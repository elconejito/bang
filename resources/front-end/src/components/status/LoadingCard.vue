<template>
  <div class="rounded border border-line bg-surface shadow-sm">
    <LoadingState :message="message || 'Loading…'" />
  </div>
</template>
<script setup>
defineProps({
  message: { type: String, default: '' },
});
</script>
