<template>
  <div class="rounded border border-red-200 bg-white shadow-sm">
    <div class="p-4 text-center text-xl text-red-400">
      <font-awesome-icon icon="exclamation-triangle" />
    </div>
    <div v-if="message" class="border-t border-red-100 p-4 text-sm text-gray-700">
      {{ message }}
      <ul v-if="errors" class="mt-1 list-disc pl-4">
        <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  error: { type: Error, default: null },
});

const message = computed(() => {
  if (props.error?.response?.data) {
    return props.error.response.data.message ?? 'Unknown error';
  }
  return 'Unknown error';
});

const errors = computed(() => {
  if (props.error?.errorBag) {
    return Object.keys(props.error.errorBag).map((key) => props.error.errorBag[key][0]);
  }
  return null;
});
</script>
