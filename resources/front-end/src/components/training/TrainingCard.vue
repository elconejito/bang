<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <h3 class="font-medium">
        <router-link
          :to="{ name: 'TrainingShow', params: { training_id: training.id } }"
          class="text-blue-600 hover:text-blue-700"
        >
          {{ training.label }}
        </router-link>
      </h3>
    </div>
    <div class="px-4 py-3 text-sm text-gray-600 space-y-1">
      <p v-if="training.session_date">{{ training.session_date }}</p>
      <p v-if="training.description" class="text-gray-500 line-clamp-2">{{ training.description }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  training: {
    type: Object,
    required: true,
  },
})
</script>
