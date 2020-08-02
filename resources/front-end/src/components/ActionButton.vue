<template>
  <button type="button" class="btn" @click="$emit('click')">
    {{ text }}
    <font-awesome-icon v-if="isLoading" icon="spinner" class="ml-1" spin />
  </button>
</template>

<script>
export default {
  name: 'ActionButton',
  props: {
    text: {
      type: String,
      required: false,
      default: '',
    },
    isLoading: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
};
</script>

<style scoped></style>
