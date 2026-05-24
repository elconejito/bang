<template>
  <div>
    <TextEditor v-model="note" />
    <button type="button" class="mt-2 btn btn-outline-primary" @click="addNote">
      Add Note
    </button>
    <NotesList :notes="notes" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import NotesList from '@/components/notes/NotesList.vue'
import TextEditor from '@/components/TextEditor.vue'

const props = defineProps({
  firearm: {
    type: Object,
    required: true,
  },
})

const firearmsStore = useFirearmsStore()
const note = ref(null)
const notes = ref([])

onMounted(() => fetchNotes())

async function addNote() {
  await firearmsStore.createNote(props.firearm.id, { note: note.value })
  note.value = null
  await fetchNotes()
}

async function fetchNotes() {
  const { data } = await firearmsStore.fetchNotes(props.firearm.id, {
    orderBy: 'updated_at',
    sortedBy: 'desc',
  })
  notes.value = data
}
</script>
