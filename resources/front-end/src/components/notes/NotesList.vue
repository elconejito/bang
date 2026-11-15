<template>
  <div>
    <div v-if="notes.length === 0" class="mx-auto max-w-sm px-4 py-8">
      <Empty :message="emptyMessage" />
    </div>
    <NoteCard
      v-for="note in notes"
      :key="note.id"
      :note="note"
      :update-note="updateNote"
      :delete-note="deleteNote"
    />
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
  updateNote: { type: Function, required: true },
  deleteNote: { type: Function, required: true },
  emptyMessage: {
    type: String,
    default: 'No notes',
  },
});
</script>
