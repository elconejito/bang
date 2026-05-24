<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="p-4 text-center text-xl text-gray-400">
      <Loading />
    </div>
    <div v-if="message" class="border-t border-gray-100 p-4 text-center text-sm text-gray-500">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
import Loading from '@/components/Loading.vue'

defineProps({
  message: { type: String, default: '' },
})
</script>
