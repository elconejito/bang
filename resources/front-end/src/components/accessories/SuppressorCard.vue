<script setup>
import { useRouter } from 'vue-router';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  suppressor: { type: Object, required: true },
});

const router = useRouter();

function goToEdit(e) {
  e.preventDefault();
  e.stopPropagation();
  router.push({
    name: props.suppressor.status === 'archived' ? 'SuppressorShow' : 'SuppressorEdit',
    params: { suppressor_id: props.suppressor.id },
  });
}
</script>

<template>
  <router-link
    :to="{ name: 'SuppressorShow', params: { suppressor_id: suppressor.id } }"
    class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden flex flex-col cursor-pointer hover:border-[#c2c6ca] hover:shadow-md transition-all duration-150"
  >
    <div class="p-[14px_16px] flex flex-col gap-2 flex-1">
      <div class="flex items-center justify-between">
        <span
          class="font-mono text-[9px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5"
        >
          SUPPRESSOR
          <span v-if="suppressor.is_nfa" class="text-white bg-[#1a1c1f] rounded-[2px] px-1"
            >NFA</span
          >
        </span>
        <span
          v-if="suppressor.status === 'archived'"
          class="rounded-sm border border-[#dfc98d] bg-[#fbf6e8] px-[7px] py-[2px] font-mono text-[9px] uppercase tracking-[0.06em] text-[#7d6320]"
          >Archived · {{ suppressor.archive_reason }}</span
        >
        <span v-if="suppressor.serial" class="font-mono text-[10px] text-[#8a9098]">
          SN ·{{ suppressor.serial.slice(-4) }}
        </span>
      </div>
      <div>
        <div class="font-display font-semibold text-lg leading-tight">{{ suppressor.label }}</div>
        <div class="text-[13px] text-[#6b7077]">{{ suppressor.manufacturer }}</div>
      </div>
      <div class="flex gap-1.5 flex-wrap mt-auto">
        <span
          v-if="suppressor.caliber"
          class="text-[12px] border border-[#c2c6ca] rounded-sm px-[9px] py-[1px] text-[#3a3e44] bg-[#f5f6f7]"
        >
          {{ suppressor.caliber.label }}
        </span>
        <span
          v-if="suppressor.mount_type"
          class="text-[12px] border border-[#c2c6ca] rounded-sm px-[9px] py-[1px] text-[#3a3e44] bg-[#f5f6f7]"
        >
          {{ suppressor.mount_type }}
        </span>
      </div>
    </div>
    <div
      class="border-t border-[#eef0f1] flex items-center justify-between px-4 py-[9px] bg-[#fafbfb]"
    >
      <span
        v-if="suppressor.firearm"
        class="font-mono text-[10px] text-[#2f7d57] border border-[#9ccbb1] bg-[#e7f1eb] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5"
      >
        <span class="w-[5px] h-[5px] rounded-full bg-[#2f7d57]" />
        ON · {{ suppressor.firearm.label ?? suppressor.firearm.manufacturer }}
      </span>
      <span
        v-else
        class="font-mono text-[10px] text-[#5b6066] border border-[#c2c6ca] bg-[#f5f6f7] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5"
      >
        <span class="w-[5px] h-[5px] rounded-full border-[1.5px] border-[#8a9098]" />
        OFF · {{ suppressor.location?.label ?? 'Unmounted' }}
      </span>
      <button
        class="inline-flex items-center gap-1 text-[13px] font-semibold text-[#7d6320]"
        @click="goToEdit"
      >
        {{ suppressor.status === 'archived' ? 'View' : suppressor.firearm ? 'Move' : 'Mount' }}
        <ChevronRight class="h-[13px] w-[13px]" />
      </button>
    </div>
  </router-link>
</template>
