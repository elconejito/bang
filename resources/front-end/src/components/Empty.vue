<template>
  <div class="empty-container text-center">
    <font-awesome-icon icon="battery-empty" />
    <div>{{ message }}</div>
  </div>
</template>

<script>
export default {
  name: 'Empty',
  props: {
    message: {
      type: String,
      required: true,
    },
  },
};
</script>

<style></style>
