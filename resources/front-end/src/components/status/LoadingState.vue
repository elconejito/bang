<template>
  <div
    class="flex min-h-32 items-center justify-center px-6 py-10"
    role="status"
    aria-live="polite"
    :aria-label="message"
  >
    <div class="flex items-center gap-3 rounded border border-line bg-surface px-4 py-3 shadow-sm">
      <span
        class="h-5 w-5 animate-spin rounded-full border-2 border-ink-200 border-t-brass-700"
        aria-hidden="true"
      />
      <span class="text-[14px] font-medium text-ink-600">{{ message }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, default: 'Loading…' },
});
</script>
