<template>
  <div class="card note-card">
    <div class="card-body" v-html="note.note"></div>
    <div class="card-footer text-muted d-flex justify-content-between">
      <div class="small">{{ timestamp }}</div>
      <div class="btn-group btn-group-sm" role="group" aria-label="Note Actions">
        <button type="button" class="btn btn-outline-primary">
          <font-awesome-icon icon="edit" />
        </button>
        <button type="button" class="btn btn-outline-danger">
          <font-awesome-icon icon="trash" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import HasDateTimes from 'mixins/HasDateTimes';

export default {
  name: 'NoteCard',
  mixins: [HasDateTimes],
  props: {
    note: {
      type: Object,
      required: true,
    },
  },
  computed: {
    timestamp() {
      return this.ago(this.note.updated_at);
    },
  },
};
</script>

<style scoped></style>
