<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  misc: { type: Object, required: true },
});

const router = useRouter();

// Items that fit/carry rather than mount to a firearm (never show OFF · Unmounted)
const FITS_TYPES = ['holster', 'case', 'bag'];

const isFits = computed(
  () => !props.misc.firearm && FITS_TYPES.includes(props.misc.sub_type?.toLowerCase())
);

function goToEdit(e) {
  e.preventDefault();
  e.stopPropagation();
  router.push({
    name: props.misc.status === 'archived' ? 'MiscShow' : 'MiscEdit',
    params: { misc_id: props.misc.id },
  });
}
</script>

<template>
  <router-link
    :to="{ name: 'MiscShow', params: { misc_id: misc.id } }"
    class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden flex flex-col cursor-pointer transition-[border-color,box-shadow] duration-150 hover:border-[#c2c6ca] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]"
  >
    <div class="p-[14px_16px] flex flex-col gap-2 flex-1">
      <span
        class="font-mono text-[9px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px] w-fit"
      >
        MISC<template v-if="misc.sub_type"> · {{ misc.sub_type.toUpperCase() }}</template>
      </span>
      <span
        v-if="misc.status === 'archived'"
        class="rounded-sm border border-[#dfc98d] bg-[#fbf6e8] px-[7px] py-[2px] font-mono text-[9px] uppercase tracking-[0.06em] text-[#7d6320]"
        >Archived · {{ misc.archive_reason }}</span
      >
      <div>
        <div class="font-display font-semibold text-lg leading-tight">{{ misc.label }}</div>
        <div class="text-[13px] text-[#6b7077]">{{ misc.manufacturer }}</div>
      </div>
    </div>
    <div
      class="border-t border-[#eef0f1] flex items-center justify-between px-4 py-[9px] bg-[#fafbfb]"
    >
      <!-- ON: mounted to a firearm -->
      <span
        v-if="misc.firearm"
        class="font-mono text-[10px] text-[#2f7d57] border border-[#9ccbb1] bg-[#e7f1eb] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5"
      >
        <span class="w-[5px] h-[5px] rounded-full bg-[#2f7d57]" />
        ON · {{ misc.firearm.label ?? misc.firearm.manufacturer }}
      </span>
      <!-- FITS: carry/holster type with a location -->
      <span
        v-else-if="isFits"
        class="font-mono text-[10px] text-[#3a3e44] border border-[#b6bcc1] bg-white rounded-sm px-[7px] py-[2px]"
      >
        FITS · {{ misc.location?.full_label ?? misc.location?.label ?? 'Unassigned' }}
      </span>
      <!-- OFF: unmounted -->
      <span
        v-else
        class="font-mono text-[10px] text-[#5b6066] border border-[#c2c6ca] bg-[#f5f6f7] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5"
      >
        <span class="w-[5px] h-[5px] rounded-full border-[1.5px] border-[#8a9098]" />
        OFF · {{ misc.location?.full_label ?? misc.location?.label ?? 'Unmounted' }}
      </span>
      <button
        class="inline-flex items-center gap-1 text-[13px] font-semibold text-[#7d6320]"
        @click="goToEdit"
      >
        {{
          misc.status === 'archived' ? 'View' : misc.firearm ? 'Move' : isFits ? 'Edit' : 'Mount'
        }}
        <ChevronRight class="h-[13px] w-[13px]" />
      </button>
    </div>
  </router-link>
</template>
