<template>
  <div>
    <TextEditor v-model="note" />
    <button type="button" class="btn btn-outline-primary" @click="addNote">
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
  mounted() {},
  methods: {
    addNote() {
      console.log('FirearmNotes addNote()');
      const payload = {
        ammunitionId: this.ammunition.id,
        data: {
          note: this.note,
        },
      };

      this.$store
        .dispatch('ammunition/storeNote', payload)
        .then((response) => {
          console.log('FirearmNotes addNote() storeNote.then', response);
        })
        .catch((error) => {
          console.log('FirearmNotes addNote() storeNote.catch', error);
        })
        .finally(() => {
          console.log('FirearmNotes addNote() storeNote.finally');
        });
    },
    fetchNotes() {},
  },
};
</script>
