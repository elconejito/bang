<script setup>
defineProps({
  name: { type: String, required: true },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  apply: { type: Boolean, default: false },
  status: { type: String, required: true },
  summary: { type: String, required: true },
  inputId: { type: String, required: true },
  error: { type: String, default: '' },
});

const emit = defineEmits(['update:apply']);
</script>

<template>
  <fieldset
    class="rounded border p-3 transition-colors"
    :class="apply ? 'border-brass-300 bg-brass-50/60' : 'border-line bg-white'"
  >
    <legend class="sr-only">{{ title }}</legend>
    <div class="flex items-start gap-3">
      <input
        :id="`${inputId}-apply`"
        :checked="apply"
        type="checkbox"
        class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#c2c6ca] accent-brass"
        :aria-label="`Apply ${title}`"
        @change="emit('update:apply', $event.target.checked)"
      />
      <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <label :for="`${inputId}-apply`" class="text-sm font-semibold text-ink-900">{{
            title
          }}</label>
          <span
            class="rounded px-2 py-0.5 font-mono text-[10px] font-semibold uppercase tracking-wide"
            :class="{
              'bg-ink-100 text-ink-600': status === 'KEEP',
              'bg-brass-200 text-brass-900': status === 'WILL CHANGE',
              'bg-caution-bg text-caution': status === 'WILL CLEAR',
              'bg-success-bg text-success': status === 'NO CHANGE',
            }"
          >
            {{ status }}
          </span>
        </div>
        <p class="mt-1 text-xs text-muted">{{ summary }}</p>
        <p v-if="description" class="mt-1 text-xs text-ink-500">{{ description }}</p>
        <p v-if="error" :id="`${inputId}-error`" class="mt-1 text-xs text-caution">{{ error }}</p>
        <div v-if="apply" class="mt-3">
          <slot :aria-describedby="error ? `${inputId}-error` : undefined" />
        </div>
      </div>
    </div>
  </fieldset>
</template>
