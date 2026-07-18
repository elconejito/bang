<script setup>
import dayjs from 'dayjs';
import { Archive } from 'lucide-vue-next';

defineProps({
  reason: { type: String, required: true },
  description: { type: String, default: null },
  archivedAt: { type: String, required: true },
});

function titleCase(value) {
  return value.replaceAll('_', ' ').replace(/\b\w/g, (character) => character.toUpperCase());
}
</script>

<template>
  <section
    class="mb-5 flex items-start gap-3 rounded border border-[#dfc98d] bg-[#fbf6e8] px-4 py-3.5"
    role="status"
  >
    <span
      class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded border border-[#dfc98d] bg-white text-[#7d6320]"
    >
      <Archive class="h-4 w-4" />
    </span>
    <div class="min-w-0">
      <p class="font-display text-[16px] font-semibold text-[#604c18]">
        Archived — {{ titleCase(reason) }}
      </p>
      <p v-if="description" class="mt-1 whitespace-pre-wrap text-[14px] text-[#574b2c]">
        {{ description }}
      </p>
      <p class="mt-1.5 text-[12px] font-medium uppercase tracking-[0.04em] text-[#8a7540]">
        Archived {{ dayjs(archivedAt).format('MMM D, YYYY') }}
      </p>
    </div>
  </section>
</template>
