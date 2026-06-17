<script setup>
defineProps({
  misc: { type: Object, required: true },
});
</script>

<template>
  <router-link
    :to="{ name: 'MiscShow', params: { misc_id: misc.id } }"
    class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden flex flex-col cursor-pointer hover:border-[#c2c6ca] hover:shadow-md transition-all duration-150"
  >
    <div class="p-[14px_16px] flex flex-col gap-2 flex-1">
      <span class="font-mono text-[9px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px] w-fit">
        MISC<template v-if="misc.sub_type"> · {{ misc.sub_type.toUpperCase() }}</template>
      </span>
      <div>
        <div class="font-display font-semibold text-lg leading-tight">{{ misc.label }}</div>
        <div class="text-[13px] text-[#6b7077]">{{ misc.manufacturer }}</div>
      </div>
    </div>
    <div class="border-t border-[#eef0f1] flex items-center justify-between px-4 py-[9px] bg-[#fafbfb]">
      <span v-if="misc.firearm" class="font-mono text-[10px] text-[#2f7d57] border border-[#9ccbb1] bg-[#e7f1eb] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5">
        <span class="w-[5px] h-[5px] rounded-full bg-[#2f7d57]" />
        ON · {{ misc.firearm.label ?? misc.firearm.manufacturer }}
      </span>
      <span v-else class="font-mono text-[10px] text-[#5b6066] border border-[#c2c6ca] bg-[#f5f6f7] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5">
        <span class="w-[5px] h-[5px] rounded-full border-[1.5px] border-[#8a9098]" />
        OFF · {{ misc.location?.label ?? 'Unmounted' }}
      </span>
    </div>
  </router-link>
</template>
