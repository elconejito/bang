<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="p-4" v-html="note.note"></div>
    <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3">
      <div class="text-sm text-gray-500">{{ timestamp }}</div>
      <div class="flex gap-1">
        <button
          type="button"
          class="rounded border border-blue-600 p-1.5 text-blue-600 transition-colors hover:bg-blue-50"
        >
          <font-awesome-icon icon="edit" />
        </button>
        <button
          type="button"
          class="rounded border border-red-600 p-1.5 text-red-600 transition-colors hover:bg-red-50"
        >
          <font-awesome-icon icon="trash" />
        </button>
      </div>
    </div>
  </div>
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
const timestamp = computed(() => ago(props.note.updated_at));
</script>
