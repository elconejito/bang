<template>
  <div class="row">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="notes.length === 0">
      <Empty message="No notes" />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(note, i) in notes" :key="i">
      <NoteCard :note="note" />
    </div>
  </div>
</template>

<script>
import Empty from '../Empty';
import NoteCard from 'components/notes/NoteCard';

export default {
  name: 'NotesList',
  components: { NoteCard, Empty },
  props: {
    notes: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped></style>
