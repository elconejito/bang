<template>
  <div
    class="flex items-start gap-2.5 rounded border border-caution-border bg-caution-bg p-4 text-caution"
    role="alert"
  >
    <TriangleAlert class="mt-0.5 h-[15px] w-[15px] shrink-0" />
    <div class="text-[14px]">
      {{ message }}
      <ul v-if="errors" class="mt-1 list-disc pl-4">
        <li v-for="(fieldError, i) in errors" :key="i">{{ fieldError }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { TriangleAlert } from 'lucide-vue-next';

const props = defineProps({
  error: { type: Error, default: null },
});

const message = computed(() => {
  const data = props.error?.response?.data;
  if (data) {
    return data.message ?? data.error ?? 'An unexpected error occurred.';
  }
  return props.error?.message ?? 'An unexpected error occurred.';
});

const errors = computed(() => {
  if (props.error?.errorBag) {
    return Object.keys(props.error.errorBag).map((key) => props.error.errorBag[key][0]);
  }
  return null;
});
</script>
