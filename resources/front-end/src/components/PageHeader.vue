<template>
  <div class="flex items-start justify-between gap-4">
    <div class="flex items-center gap-3">
      <h1 class="font-display text-[28px] font-bold leading-tight tracking-tight text-ink-900">
        {{ title }}
      </h1>
      <span
        v-if="count !== undefined"
        class="rounded bg-ink-100 px-2 py-0.5 font-mono text-[11px] font-medium uppercase tracking-widest text-muted"
      >{{ count }}</span>
    </div>
    <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2 pt-1">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  count: {
    type: [Number, String],
    default: undefined,
  },
})
</script>
