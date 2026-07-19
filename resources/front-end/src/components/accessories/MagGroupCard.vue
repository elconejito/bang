<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { ChevronDown, ChevronUp, Pencil } from 'lucide-vue-next';

const props = defineProps({
  group: { type: Object, required: true },
});

const router = useRouter();
const expanded = ref(false);
const emptiesExpanded = ref(false);

const total = computed(() => props.group.magazines.length);
const inGunMags = computed(() => props.group.magazines.filter((m) => m.status === 'in_gun'));
const loadedMags = computed(() => props.group.magazines.filter((m) => m.status === 'loaded'));
const emptyMags = computed(() => props.group.magazines.filter((m) => m.status === 'empty'));

const inGunPct = computed(() =>
  total.value ? Math.round((inGunMags.value.length / total.value) * 100) : 0
);
const loadedPct = computed(() =>
  total.value ? Math.round((loadedMags.value.length / total.value) * 100) : 0
);
const emptyPct = computed(() => 100 - inGunPct.value - loadedPct.value);

function markingLabel(mag, globalIdx) {
  return mag.id_marking || mag.serial_number || `#${globalIdx + 1}`;
}

function goToDetail(e, mag) {
  e.stopPropagation();
  router.push({ name: 'MagazinesShow', params: { magazine_id: mag.id } });
}

function toggle(e) {
  e.stopPropagation();
  expanded.value = !expanded.value;
  if (!expanded.value) {
    emptiesExpanded.value = false;
  }
}
</script>

<template>
  <div
    class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden"
    :class="
      !expanded &&
      'cursor-pointer transition-[border-color,box-shadow] duration-150 hover:border-[#c2c6ca] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]'
    "
    @click="!expanded && (expanded = true)"
  >
    <!-- Group header (always visible) -->
    <div
      class="flex items-center gap-3.5 px-[18px] py-3.5 flex-wrap"
      :class="expanded ? 'border-b border-[#eef0f1]' : ''"
    >
      <!-- Name + subtitle -->
      <div class="min-w-[160px]">
        <div class="font-display font-semibold text-[18px] leading-tight">
          {{ group.model_name }}
        </div>
        <div class="text-[13px] text-[#6b7077]">
          {{ group.manufacturer }}
          <template v-if="group.caliber_label"> · {{ group.caliber_label }}</template>
          · {{ group.capacity }} rd
        </div>
      </div>

      <!-- Status bar -->
      <div class="flex-1 min-w-[200px] max-w-[340px]">
        <div class="flex h-[10px] border border-[#d6d9dc] rounded-full overflow-hidden">
          <div v-if="inGunPct" :style="{ width: inGunPct + '%' }" class="bg-[#2f7d57]" />
          <div v-if="loadedPct" :style="{ width: loadedPct + '%' }" class="bg-[#c2a14d]" />
          <div v-if="emptyPct" :style="{ width: emptyPct + '%' }" class="bg-[#eceef0]" />
        </div>
        <div class="flex gap-3 mt-[7px] text-[12px] text-[#5b6066] flex-wrap">
          <span
            v-if="inGunMags.length"
            class="inline-flex items-center gap-[5px] whitespace-nowrap"
          >
            <span class="w-2 h-2 rounded-full bg-[#2f7d57]" />{{ inGunMags.length }} in gun
          </span>
          <span
            v-if="loadedMags.length"
            class="inline-flex items-center gap-[5px] whitespace-nowrap"
          >
            <span class="w-2 h-2 rounded-full bg-[#c2a14d]" />{{ loadedMags.length }} loaded
          </span>
          <span
            v-if="emptyMags.length"
            class="inline-flex items-center gap-[5px] whitespace-nowrap"
          >
            <span class="w-2 h-2 rounded-full border-[1.5px] border-[#b6bcc1]" />{{
              emptyMags.length
            }}
            empty
          </span>
        </div>
      </div>

      <!-- Count + toggle -->
      <div class="ml-auto flex items-center gap-3.5">
        <span class="font-mono text-[11px] text-[#8a9098] whitespace-nowrap">{{ total }} MAGS</span>
        <button
          class="inline-flex items-center gap-[5px] text-[13px] text-[#7d6320] font-semibold whitespace-nowrap"
          @click="toggle"
        >
          {{ expanded ? 'Collapse' : 'Expand' }}
          <ChevronUp v-if="expanded" class="w-3.5 h-3.5" />
          <ChevronDown v-else class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- Expanded: individual mag table -->
    <template v-if="expanded">
      <!-- Table head -->
      <div
        class="grid grid-cols-[120px_1fr_44px] bg-[#f5f6f7] border-b border-[#e2e4e6] font-mono text-[10px] tracking-[0.05em] text-[#6b7077]"
      >
        <div class="px-[18px] py-[9px]">MARKING</div>
        <div class="px-3 py-[9px]">STATE</div>
        <div />
      </div>

      <!-- In gun rows -->
      <div
        v-for="(mag, i) in inGunMags"
        :key="mag.id"
        class="grid grid-cols-[120px_1fr_44px] items-center border-b border-[#f1f2f3] text-[15px]"
      >
        <div class="px-[18px] py-[11px] font-mono text-[14px]">{{ markingLabel(mag, i) }}</div>
        <div class="px-3 py-[11px] flex items-center gap-2">
          <span class="w-[11px] h-[11px] rounded-full bg-[#2f7d57] flex-none" />In gun
        </div>
        <button
          class="flex items-center justify-center py-[11px] text-[#8a9098] hover:text-[#1a1c1f] transition-colors"
          @click="goToDetail($event, mag)"
        >
          <Pencil class="w-4 h-4" />
        </button>
      </div>

      <!-- Loaded rows -->
      <div
        v-for="(mag, i) in loadedMags"
        :key="mag.id"
        class="grid grid-cols-[120px_1fr_44px] items-center border-b border-[#f1f2f3] text-[15px]"
      >
        <div class="px-[18px] py-[11px] font-mono text-[14px]">
          {{ markingLabel(mag, inGunMags.length + i) }}
        </div>
        <div class="px-3 py-[11px] flex items-center gap-2">
          <span class="w-[11px] h-[11px] rounded-full bg-[#c2a14d] flex-none" />Loaded
        </div>
        <button
          class="flex items-center justify-center py-[11px] text-[#8a9098] hover:text-[#1a1c1f] transition-colors"
          @click="goToDetail($event, mag)"
        >
          <Pencil class="w-4 h-4" />
        </button>
      </div>

      <!-- Empty rows -->
      <template v-if="emptyMags.length">
        <!-- Individual empty rows when expanded -->
        <template v-if="emptiesExpanded">
          <div
            v-for="(mag, i) in emptyMags"
            :key="mag.id"
            class="grid grid-cols-[120px_1fr_44px] items-center border-b border-[#f1f2f3] text-[15px] text-[#6b7077]"
          >
            <div class="px-[18px] py-[11px] font-mono text-[13px]">
              {{ markingLabel(mag, inGunMags.length + loadedMags.length + i) }}
            </div>
            <div class="px-3 py-[11px] flex items-center gap-2">
              <span
                class="w-[11px] h-[11px] rounded-full border-[1.5px] border-[#b6bcc1] flex-none"
              />Empty
            </div>
            <button
              class="flex items-center justify-center py-[11px] text-[#8a9098] hover:text-[#1a1c1f] transition-colors"
              @click="goToDetail($event, mag)"
            >
              <Pencil class="w-4 h-4" />
            </button>
          </div>
          <!-- Collapse empties row -->
          <div
            class="grid grid-cols-[120px_1fr_44px] items-center bg-[#fafbfb] text-[15px] cursor-pointer hover:bg-[#f5f6f7] transition-colors border-b border-[#f1f2f3]"
            @click="emptiesExpanded = false"
          >
            <div class="px-[18px] py-[11px] font-mono text-[13px] text-[#6b7077]" />
            <div class="px-3 py-[11px] flex items-center gap-2 text-[#6b7077] text-[13px]">
              Collapse empties
            </div>
            <div class="flex items-center justify-center py-[11px] text-[#7d6320]">
              <ChevronUp class="w-4 h-4" />
            </div>
          </div>
        </template>

        <!-- Collapsed empty summary row -->
        <div
          v-else
          class="grid grid-cols-[120px_1fr_44px] items-center bg-[#fafbfb] text-[15px] cursor-pointer hover:bg-[#f5f6f7] transition-colors border-b border-[#f1f2f3]"
          @click="emptiesExpanded = true"
        >
          <div class="px-[18px] py-[11px] font-mono text-[13px] text-[#6b7077]">
            {{
              emptyMags.length === 1
                ? markingLabel(emptyMags[0], inGunMags.length + loadedMags.length)
                : `${emptyMags.length} empty`
            }}
          </div>
          <div class="px-3 py-[11px] flex items-center gap-2 text-[#6b7077]">
            <span
              class="w-[11px] h-[11px] rounded-full border-[1.5px] border-[#b6bcc1] flex-none"
            />
            {{ emptyMags.length }} empty
          </div>
          <div class="flex items-center justify-center py-[11px] text-[#7d6320]">
            <ChevronDown class="w-4 h-4" />
          </div>
        </div>
      </template>

      <!-- Footer: add another -->
      <div class="px-[18px] py-[11px]">
        <router-link
          :to="{ name: 'MagazinesCreate' }"
          class="inline-flex items-center gap-1.5 text-[14px] text-[#7d6320] font-semibold"
          @click.stop
        >
          <svg
            class="w-3.5 h-3.5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M5 12h14" />
            <path d="M12 5v14" />
          </svg>
          Add another {{ group.model_name }}
        </router-link>
      </div>
    </template>
  </div>
</template>
