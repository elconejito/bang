<template>
  <div>
    <TextEditor v-model="note" />
    <button
      type="button"
      class="mt-2 inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1.5 text-sm text-blue-600 transition-colors hover:bg-blue-50"
      @click="addNote"
    >Add Note</button>
    <NotesList :notes="notes" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAmmunitionStore } from '@/stores/ammunition'
import NotesList from '@/components/notes/NotesList.vue'
import TextEditor from '@/components/TextEditor.vue'

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
  caliber: {
    type: Object,
    required: true,
  },
})

const ammunitionStore = useAmmunitionStore()
const note = ref(null)
const notes = ref([])

onMounted(() => fetchNotes())

async function addNote() {
  await ammunitionStore.createNote(props.ammunition.id, { note: note.value })
  note.value = null
  await fetchNotes()
}

async function fetchNotes() {
  const { data } = await ammunitionStore.fetchNotes(props.ammunition.id, {
    orderBy: 'updated_at',
    sortedBy: 'desc',
  })
  notes.value = data
}
</script>
