<template>
  <article class="border-b border-[#eef0f1] px-4 py-3.5 last:border-b-0">
    <div class="whitespace-pre-wrap break-words text-[14px] leading-[1.6] text-[#3a3e44]">
      {{ note.note }}
    </div>
    <div class="mt-2 font-mono text-[10px] tracking-[0.03em] text-muted">{{ timestamp }}</div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { useDateTimes } from '@/composables/useDateTimes';

const props = defineProps({
  note: {
    type: Object,
    required: true,
  },
});

const { ago } = useDateTimes();
const timestamp = computed(() => ago(props.note.created_at));
</script>
