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

<script setup>
import { computed } from 'vue'
import { useDateTimes } from '@/composables/useDateTimes'

const props = defineProps({
  note: {
    type: Object,
    required: true,
  },
})

const { ago } = useDateTimes()
const timestamp = computed(() => ago(props.note.updated_at))
</script>
