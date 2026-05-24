<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <h3 class="font-medium text-gray-900">{{ store.label }}</h3>
    </div>
    <div class="px-4 py-3 text-sm text-gray-600">
      <p v-if="store.type">{{ store.type }}</p>
      <p v-else class="text-gray-400">Info TBD</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  store: {
    type: Object,
    required: true,
  },
})
</script>
