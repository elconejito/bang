<template>
  <div class="flex flex-wrap items-center gap-[14px]">
    <h1 class="font-display text-[30px] font-bold leading-none tracking-[-0.02em] text-ink-900">
      {{ title }}
    </h1>
    <span
      v-if="count !== undefined"
      class="whitespace-nowrap rounded border border-line bg-surface px-[9px] py-[3px] font-mono text-[12px] text-muted"
    >{{ count }}</span>
    <slot name="count-suffix" />
    <div v-if="$slots.actions" class="ml-auto flex shrink-0 items-center gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  count: { type: [Number, String], default: undefined },
})
</script>
