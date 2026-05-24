<template>
  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Details</h3>
      <dl class="space-y-2 text-sm">
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Location</dt>
          <dd class="col-span-2">{{ training.location_id }}</dd>
        </div>
      </dl>
    </div>
    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Notes</h3>
      <p class="text-sm text-gray-400">Coming Soon</p>
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
