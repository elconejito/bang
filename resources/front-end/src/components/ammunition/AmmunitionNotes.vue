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
  name: 'AmmunitionNotes',
  components: { NotesList, TextEditor },
  mixins: [HasLoading],
  props: {
    ammunition: {
      type: Object,
      required: true,
    },
    caliber: {
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
      console.log('AmmunitionNotes addNote()');
      const payload = {
        ammunitionId: this.ammunition.id,
        data: {
          note: this.note,
        },
      };

      this.$store
        .dispatch('ammunition/storeNote', payload)
        .then((response) => {
          console.log('AmmunitionNotes addNote() storeNote.then', response);
          this.note = null;
          this.fetchNotes();
        })
        .catch((error) => {
          console.log('AmmunitionNotes addNote() storeNote.catch', error);
        })
        .finally(() => {
          console.log('AmmunitionNotes addNote() storeNote.finally');
        });
    },
    fetchNotes() {
      console.log('AmmunitionNotes fetchNotes()');
      const payload = {
        ammunitionId: this.ammunition.id,
        params: {
          orderBy: 'updated_at',
          sortedBy: 'desc',
        },
      };

      this.$store
        .dispatch('ammunition/getNotes', payload)
        .then((response) => {
          console.log('AmmunitionNotes fetchNotes() getNotes.then', response);
          this.notes = response.data;
        })
        .catch((error) => {
          console.log('AmmunitionNotes fetchNotes() getNotes.catch', error);
        })
        .finally(() => {
          console.log('AmmunitionNotes fetchNotes() getNotes.finally');
        });
    },
  },
};
</script>
