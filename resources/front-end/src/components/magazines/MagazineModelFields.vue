<script setup>
defineProps({ manufacturerError: { type: String, default: null } });

const manufacturer = defineModel('manufacturer', { type: String });
const modelName = defineModel('modelName', { type: String });
const modelNumber = defineModel('modelNumber', { type: String });
const capacity = defineModel('capacity', { type: [Number, String] });
</script>

<template>
  <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
    <span>Manufacturer <span class="text-[#b4452f]">*</span></span>
    <input
      v-model="manufacturer"
      type="text"
      required
      class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
      placeholder="e.g. Magpul"
    />
    <span v-if="manufacturerError" class="font-normal text-red-700">{{ manufacturerError }}</span>
  </label>

  <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
    <span>Model name</span>
    <input
      v-model="modelName"
      type="text"
      class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
      placeholder="e.g. PMAG GL9"
    />
  </label>

  <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
    <span class="inline-flex items-baseline gap-1">
      Model # <span class="font-normal text-ink-400">Optional</span>
    </span>
    <input
      v-model="modelNumber"
      type="text"
      class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
      placeholder="Manufacturer model number"
    />
  </label>

  <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
    <span>Capacity <span class="text-[#b4452f]">*</span></span>
    <input
      v-model.number="capacity"
      type="number"
      min="1"
      required
      class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none placeholder:text-muted focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
      placeholder="e.g. 21"
    />
  </label>
</template>
