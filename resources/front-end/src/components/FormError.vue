<template>
  <div class="flex items-start gap-2 rounded border border-red-300 bg-red-50 p-4 text-red-700" role="alert">
    <font-awesome-icon icon="exclamation-triangle" class="mt-0.5 shrink-0" />
    <div>
      {{ message }}
      <ul v-if="errors" class="mt-1 list-disc pl-4">
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
