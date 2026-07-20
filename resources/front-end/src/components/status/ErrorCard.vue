<template>
  <section
    class="flex items-start gap-3 rounded border border-caution-border bg-caution-bg p-4 text-caution"
    role="alert"
    aria-live="assertive"
    aria-atomic="true"
    :aria-labelledby="titleId"
  >
    <TriangleAlert class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true" />
    <div class="min-w-0 flex-1">
      <h2 :id="titleId" class="font-display text-[16px] font-semibold text-caution">
        {{ title }}
      </h2>
      <p v-if="message" class="mt-1 text-[14px] leading-5 text-ink-700">
        {{ message }}
      </p>
      <ul
        v-if="errors?.length"
        class="mt-2 list-disc space-y-1 pl-4 text-[14px] leading-5 text-ink-700"
      >
        <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
      </ul>
      <button
        v-if="retryLabel"
        type="button"
        class="mt-3 inline-flex rounded border border-caution-border bg-surface px-3 py-1.5 text-[13px] font-semibold text-caution transition-colors hover:bg-white"
        @click="$emit('retry')"
      >
        {{ retryLabel }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed, useId } from 'vue';
import { TriangleAlert } from 'lucide-vue-next';

const props = defineProps({
  error: { type: Error, default: null },
  title: { type: String, default: 'Something went wrong' },
  retryLabel: { type: String, default: null },
});

defineEmits(['retry']);

const titleId = useId();

const message = computed(() => {
  if (props.error?.response?.data) {
    return props.error.response.data.message ?? 'Unknown error';
  }
  return 'Unknown error';
});

const errors = computed(() => {
  if (props.error?.errorBag) {
    return Object.keys(props.error.errorBag).map((key) => props.error.errorBag[key][0]);
  }
  return null;
});
</script>
