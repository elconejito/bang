<template>
  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Details</h3>
      <dl class="space-y-2 text-sm">
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Manufacturer</dt>
          <dd class="col-span-2">{{ firearm.manufacturer }}</dd>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Model</dt>
          <dd class="col-span-2">{{ firearm.model }}</dd>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Label</dt>
          <dd class="col-span-2">{{ firearm.label }}</dd>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <dt class="font-medium text-gray-500">Calibers</dt>
          <dd class="col-span-2">
            <span v-if="firearm.calibers.length === 0" class="text-gray-400">None</span>
            <div v-else class="flex flex-wrap gap-1">
              <router-link
                v-for="(caliber, i) in firearm.calibers"
                :key="i"
                :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
                class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800 transition-colors hover:bg-blue-200"
              >
                {{ caliber.label }}
              </router-link>
            </div>
          </dd>
        </div>
      </dl>
    </div>

    <div>
      <h3 class="mb-3 text-base font-semibold text-gray-900">Notes</h3>
      <FirearmNotes :firearm="firearm" />
    </div>
  </div>
</template>

<script setup>
import FirearmNotes from '@/components/firearms/FirearmNotes.vue'

defineProps({
  firearm: {
    type: Object,
    required: true,
  },
})
</script>
