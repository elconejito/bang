<script setup>
import { CloudAlert } from 'lucide-vue-next';

defineProps({
  status: { type: Object, default: null },
});
</script>

<template>
  <div
    v-if="status?.notice"
    class="flex items-start gap-2 rounded border border-[#d7b95f] bg-[#fff9e8] px-3 py-2.5 text-[13px] text-[#6f5718]"
    role="status"
  >
    <CloudAlert class="mt-0.5 h-4 w-4 shrink-0" />
    <span>{{ status.notice }}</span>
  </div>
</template>
