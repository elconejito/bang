<script setup>
defineProps({
  locations: { type: Array, default: () => [] },
  disabled: { type: Boolean, default: false },
});

const locationId = defineModel({ type: [Number, String], default: null });
</script>

<template>
  <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
    <span>Storage location</span>
    <select
      data-testid="magazine-storage-location"
      v-model.number="locationId"
      :disabled="disabled"
      class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
    >
      <option :value="null">Unassigned</option>
      <option v-for="location in locations" :key="location.id" :value="location.id">
        {{ location.full_label ?? location.label }}
      </option>
    </select>
  </label>
</template>
