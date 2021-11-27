<template>
  <div>
    <div>CONTENT</div>
    <div>actions</div>
  </div>
</template>

<script>
export default {
  name: 'NoteCard',
  props: {
    note: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped></style>
