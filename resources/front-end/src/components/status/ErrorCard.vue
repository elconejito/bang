<template>
  <div class="card status-card">
    <div class="card-body text-center">
      <font-awesome-icon icon="exclamation-triangle" class="me-1" />
    </div>
    <div v-if="message" class="card-body">
      {{ message }}
      <ul v-if="errors">
        <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  error: { type: Error, default: null },
})

const message = computed(() => {
  if (props.error?.response?.data) {
    return props.error.response.data.message ?? 'Unknown error'
  }
  return 'Unknown error'
})

const errors = computed(() => {
  if (props.error?.errorBag) {
    return Object.keys(props.error.errorBag).map((key) => props.error.errorBag[key][0])
  }
  return null
})
</script>

<style scoped></style>
