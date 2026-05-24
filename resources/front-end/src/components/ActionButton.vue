<template>
  <button type="button" class="btn" @click="emit('click')">
    {{ text }}
    <font-awesome-icon v-if="isLoading" icon="spinner" class="ms-1" spin />
  </button>
</template>

<script setup>
defineProps({
  text: { type: String, default: '' },
  isLoading: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])
</script>

<style scoped></style>
