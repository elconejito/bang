<script setup>
import { computed } from 'vue';

const props = defineProps({
  magazine: { type: Object, required: true },
});

const inGun = computed(() =>
  props.magazine.status === 'in_gun' ? 1 : 0
);
</script>

<template>
  <router-link
    :to="{ name: 'MagazinesShow', params: { magazine_id: magazine.id } }"
    class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden flex flex-col cursor-pointer hover:border-[#c2c6ca] hover:shadow-md transition-all duration-150"
  >
    <div class="p-[14px_16px] flex flex-col gap-2.5 flex-1">
      <div class="flex items-center justify-between">
        <span class="font-mono text-[9px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px]">
          MAGAZINE
        </span>
        <span class="font-mono text-[10px] text-[#8a9098]">{{ magazine.capacity }} rd</span>
      </div>
      <div>
        <div class="font-display font-semibold text-lg leading-tight">{{ magazine.model_name ?? magazine.label }}</div>
        <div class="text-[13px] text-[#6b7077]">
          {{ magazine.manufacturer }}
          <template v-if="magazine.calibers?.length"> · {{ magazine.calibers.map(c => c.label).join(' / ') }}</template>
        </div>
      </div>
    </div>
    <div class="border-t border-[#eef0f1] flex items-center justify-between px-4 py-[9px] bg-[#fafbfb]">
      <span class="font-mono text-[10px] text-[#8a9098]">
        <template v-if="magazine.status === 'in_gun'">In gun</template>
        <template v-else-if="magazine.status === 'loaded'">Loaded</template>
        <template v-else>Empty</template>
      </span>
      <span class="text-[13px] text-[#7d6320] font-semibold flex items-center gap-1">
        View
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </span>
    </div>
  </router-link>
</template>
