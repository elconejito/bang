<template>
  <div class="rounded border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
      <h3 class="font-medium text-gray-900">{{ magazine.label }}</h3>
      <p class="mt-0.5 text-sm">
        <span class="text-gray-500">{{ magazine.manufacturer }}</span>
        <span v-if="magazine.model_name"> · {{ magazine.model_name }}</span>
      </p>
    </div>

    <div class="divide-y divide-gray-100 px-4 py-3 space-y-3">
      <div>
        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">Used By</p>
        <div v-if="magazine.firearms.length === 0" class="text-sm text-gray-400">None</div>
        <div v-else class="flex flex-wrap gap-1">
          <router-link
            v-for="(firearm, i) in magazine.firearms"
            :key="i"
            :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
            class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800 transition-colors hover:bg-blue-200"
          >
            {{ firearm.label }}
          </router-link>
        </div>
      </div>

      <div class="pt-3">
        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">Calibers Supported</p>
        <div v-if="magazine.calibers.length === 0" class="text-sm text-gray-400">None</div>
        <div v-else class="flex flex-wrap gap-1">
          <router-link
            v-for="(caliber, i) in magazine.calibers"
            :key="i"
            :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
            class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-800 transition-colors hover:bg-blue-200"
          >
            {{ caliber.label }}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  magazine: {
    type: Object,
    required: true,
  },
})
</script>
