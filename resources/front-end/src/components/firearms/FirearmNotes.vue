<template>
  <div>
    <TextEditor v-model="note" />
    <button type="button" class="mt-2 btn btn-outline-primary" @click="addNote">
      Add Note
    </button>
    <NotesList :notes="notes" />
  </div>
</template>
<script>
import NotesList from 'components/notes/NotesList';
import TextEditor from '@/components/TextEditor';
import HasLoading from 'mixins/HasLoading';

export default {
  name: 'FirearmNotes',
  components: { NotesList, TextEditor },
  mixins: [HasLoading],
  props: {
    firearm: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      note: null,
      notes: [],
    };
  },
  mounted() {
    this.fetchNotes();
  },
  methods: {
    addNote() {
      console.log('FirearmNotes addNote()');
      const payload = {
        firearmId: this.firearm.id,
        data: {
          note: this.note,
        },
      };

      this.$store
        .dispatch('firearms/storeNote', payload)
        .then((response) => {
          console.log('FirearmNotes addNote() storeNote.then', response);
          this.note = null;
          this.fetchNotes();
        })
        .catch((error) => {
          console.log('FirearmNotes addNote() storeNote.catch', error);
        })
        .finally(() => {
          console.log('FirearmNotes addNote() storeNote.finally');
        });
    },
    fetchNotes() {
      console.log('FirearmNotes fetchNotes()');
      const payload = {
        firearmId: this.firearm.id,
        params: {
          orderBy: 'updated_at',
          sortedBy: 'desc',
        },
      };

      this.$store
        .dispatch('firearms/getNotes', payload)
        .then((response) => {
          console.log('FirearmNotes fetchNotes() getNotes.then', response);
          this.notes = response.data;
        })
        .catch((error) => {
          console.log('FirearmNotes fetchNotes() getNotes.catch', error);
        })
        .finally(() => {
          console.log('FirearmNotes fetchNotes() getNotes.finally');
        });
    },
  },
};
</script>
