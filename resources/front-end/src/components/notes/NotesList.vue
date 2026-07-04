<template>
  <div class="mt-4 space-y-3">
    <div v-if="notes.length === 0" class="mx-auto max-w-sm">
      <Empty message="No notes" />
    </div>
    <NoteCard v-for="(note, i) in notes" :key="i" :note="note" />
  </div>
</template>

<script setup>
import NoteCard from '@/components/notes/NoteCard.vue';
import Empty from '@/components/Empty.vue';

defineProps({
  notes: {
    type: Array,
    required: true,
  },
});
</script>
