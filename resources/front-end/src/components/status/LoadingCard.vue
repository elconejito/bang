<template>
  <div class="card status-card">
    <div class="card-body text-center">
      <Loading class="h3" />
    </div>
    <div v-if="message" class="card-body text-center">{{ message }}</div>
  </div>
</template>

<script setup>
import Loading from '@/components/Loading.vue'

defineProps({
  message: { type: String, default: '' },
})
</script>

<style scoped></style>
