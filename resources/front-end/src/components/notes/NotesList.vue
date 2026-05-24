<template>
  <div class="row notes-list">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="notes.length === 0">
      <Empty message="No notes" />
    </div>
    <div class="col-12" v-for="(note, i) in notes" :key="i">
      <NoteCard :note="note" />
    </div>
  </div>
</template>

<script setup>
import NoteCard from '@/components/notes/NoteCard.vue'
import Empty from '@/components/Empty.vue'

defineProps({
  notes: {
    type: Array,
    required: true,
  },
})
</script>
