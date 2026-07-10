<script setup>
import { computed } from 'vue';

const props = defineProps({
  magazines: { type: Array, required: true },
  loading: { type: Boolean, default: false },
});
const emit = defineEmits(['change-state']);

const stateLabels = { in_gun: 'In firearm', loaded: 'Loaded spare', empty: 'Empty' };

function ammunitionLabel(magazine) {
  return magazine.loaded_ammunition
    ? [magazine.loaded_ammunition.manufacturer, magazine.loaded_ammunition.label]
        .filter(Boolean)
        .join(' ')
    : '—';
}

function effectiveLocation(magazine) {
  if (magazine.current_firearm) {
    return [magazine.current_firearm.manufacturer, magazine.current_firearm.label]
      .filter(Boolean)
      .join(' ');
  }

  return magazine.location?.label ?? 'Unassigned';
}

const hasRows = computed(() => props.magazines.length > 0);
</script>

<template>
  <div class="overflow-hidden rounded border border-line bg-white">
    <div v-if="loading" class="px-5 py-12 text-center text-sm text-muted">Loading magazines…</div>
    <div v-else-if="!hasRows" class="px-5 py-12 text-center text-sm text-muted">
      No magazines match these filters.
    </div>
    <template v-else>
      <div
        class="hidden grid-cols-[1fr_130px_1.4fr_130px_1.2fr_70px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
      >
        <div class="px-4 py-2.5">Marking</div>
        <div class="px-3 py-2.5">State</div>
        <div class="px-3 py-2.5">Loaded With</div>
        <div class="px-3 py-2.5 text-right">Rounds Loaded</div>
        <div class="px-3 py-2.5">Location</div>
        <div class="px-4 py-2.5 text-right">Action</div>
      </div>

      <div
        v-for="magazine in magazines"
        :key="magazine.id"
        class="grid gap-3 border-b border-ink-100 px-4 py-4 last:border-b-0 md:grid-cols-[1fr_130px_1.4fr_130px_1.2fr_70px] md:items-center md:gap-0 md:px-0 md:py-0"
      >
        <div class="md:px-4 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Marking</span
          >
          <span class="font-mono text-sm font-medium text-ink-900">{{
            magazine.id_marking || '—'
          }}</span>
        </div>
        <div class="md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >State</span
          >
          <span
            class="inline-flex rounded border px-2 py-0.5 text-xs font-medium"
            :class="{
              'border-[#b9d9c8] bg-[#edf7f1] text-[#276746]': magazine.display_status === 'in_gun',
              'border-[#e3d3a3] bg-[#f4ecd6] text-[#7d6320]': magazine.display_status === 'loaded',
              'border-line bg-ink-50 text-ink-600': magazine.display_status === 'empty',
            }"
            >{{ stateLabels[magazine.display_status] ?? magazine.display_status }}</span
          >
        </div>
        <div class="text-sm text-ink-700 md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Loaded With</span
          >
          {{ ammunitionLabel(magazine) }}
        </div>
        <div class="font-mono text-sm text-ink-700 md:px-3 md:py-3 md:text-right">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Rounds Loaded</span
          >
          {{ magazine.loaded_rounds }} / {{ magazine.capacity }}
        </div>
        <div class="text-sm text-ink-700 md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Location</span
          >
          {{ effectiveLocation(magazine) }}
        </div>
        <div class="md:px-4 md:py-3 md:text-right">
          <button
            type="button"
            class="text-sm font-semibold text-brass-800 underline-offset-2 hover:underline"
            @click="emit('change-state', magazine)"
          >
            Manage
          </button>
        </div>
      </div>
    </template>
  </div>
</template>
