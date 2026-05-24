<template>
  <div class="py-8 text-center text-gray-400">
    <font-awesome-icon icon="battery-empty" class="mb-2 text-2xl" />
    <div>{{ message }}</div>
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, required: true },
})
</script>
