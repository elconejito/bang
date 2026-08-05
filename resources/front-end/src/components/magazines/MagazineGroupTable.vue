<script setup>
import { computed } from 'vue';
import { Eye, Pencil, Settings2 } from 'lucide-vue-next';

const props = defineProps({
  magazines: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  bulkMode: { type: Boolean, default: false },
  selectedIds: { type: Array, default: () => [] },
});
const emit = defineEmits(['change-state', 'toggle-select', 'toggle-select-all']);

const stateLabels = { in_gun: 'In a gun', loaded: 'Loaded spare', empty: 'Empty' };

function colorLabel(magazine) {
  return magazine.color?.label ?? '—';
}

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

  return magazine.location?.full_label ?? magazine.location?.label ?? 'Unassigned';
}

const hasRows = computed(() => props.magazines.length > 0);
const selectableMagazines = computed(() =>
  props.magazines.filter((magazine) => magazine.lifecycle_status === 'active')
);
const selectedIdSet = computed(() => new Set(props.selectedIds.map((id) => Number(id))));
const selectedCount = computed(
  () =>
    selectableMagazines.value.filter((magazine) => selectedIdSet.value.has(Number(magazine.id)))
      .length
);
const allSelected = computed(
  () =>
    selectableMagazines.value.length > 0 && selectedCount.value === selectableMagazines.value.length
);
const someSelected = computed(() => selectedCount.value > 0 && !allSelected.value);

function isSelected(magazine) {
  return selectedIdSet.value.has(Number(magazine.id));
}

function toggleRow(magazine) {
  if (props.bulkMode && magazine.lifecycle_status === 'active') {
    emit('toggle-select', magazine);
  }
}

function toggleAll() {
  emit('toggle-select-all', !allSelected.value);
}
</script>

<template>
  <div class="overflow-hidden rounded border border-line bg-white">
    <div
      v-if="bulkMode"
      class="flex items-center gap-3 border-b border-line bg-ink-50 px-4 py-2.5 text-xs text-muted"
    >
      <input
        type="checkbox"
        aria-label="Select all magazines on this page"
        :aria-checked="someSelected ? 'mixed' : String(allSelected)"
        :checked="allSelected"
        :indeterminate="someSelected"
        :disabled="selectableMagazines.length === 0"
        class="h-4 w-4 rounded border-[#c2c6ca] accent-brass"
        @click.stop
        @change="toggleAll"
      />
      <span>Select all active magazines on this page</span>
    </div>
    <div v-if="loading" class="px-5 py-12 text-center text-sm text-muted">Loading magazines…</div>
    <div v-else-if="!hasRows" class="px-5 py-12 text-center text-sm text-muted">
      No magazines match these filters.
    </div>
    <template v-else>
      <div
        class="hidden grid-cols-[1.25fr_1fr_100px_110px_1.1fr_100px_1.25fr_120px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
      >
        <div class="flex items-center gap-3 px-4 py-2.5">
          <span>Marking</span>
        </div>
        <div class="px-3 py-2.5">Nickname</div>
        <div class="px-3 py-2.5">Color</div>
        <div class="px-3 py-2.5">State</div>
        <div class="px-3 py-2.5">Loaded With</div>
        <div class="px-3 py-2.5 text-right">Rounds</div>
        <div class="px-3 py-2.5">Location</div>
        <div class="px-4 py-2.5 text-right">Actions</div>
      </div>

      <div
        v-for="magazine in magazines"
        :key="magazine.id"
        class="grid gap-3 border-b border-ink-100 px-4 py-4 last:border-b-0 md:grid-cols-[1.25fr_1fr_100px_110px_1.1fr_100px_1.25fr_120px] md:items-center md:gap-0 md:px-0 md:py-0"
        :class="{
          'cursor-pointer bg-brass-50/50': bulkMode && isSelected(magazine),
          'cursor-pointer hover:bg-ink-50': bulkMode && magazine.lifecycle_status === 'active',
          'bg-ink-50/60 text-muted': magazine.lifecycle_status === 'archived',
        }"
        :aria-selected="bulkMode ? isSelected(magazine) : undefined"
        :data-testid="`magazine-row-${magazine.id}`"
        @click="toggleRow(magazine)"
      >
        <div class="flex items-start gap-3 md:px-4 md:py-3">
          <input
            v-if="bulkMode && magazine.lifecycle_status === 'active'"
            type="checkbox"
            :aria-label="`Select magazine ${magazine.id_marking || magazine.id}`"
            :checked="isSelected(magazine)"
            :aria-checked="isSelected(magazine)"
            class="mt-0.5 h-4 w-4 shrink-0 rounded border-[#c2c6ca] accent-brass"
            @click.stop
            @change="emit('toggle-select', magazine)"
          />
          <div class="min-w-0">
            <span
              class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
              >Marking</span
            >
            <span class="block font-mono text-sm font-medium text-ink-900">{{
              magazine.id_marking || '—'
            }}</span>
            <span
              v-if="magazine.lifecycle_status === 'archived'"
              class="mt-1 block w-fit rounded border border-[#dfc98d] bg-[#fbf6e8] px-2 py-0.5 font-mono text-[10px] uppercase tracking-wide text-[#7d6320]"
            >
              Archived
            </span>
          </div>
        </div>
        <div class="min-w-0 text-sm text-ink-700 md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Nickname</span
          >
          <span class="block truncate">{{ magazine.label || '—' }}</span>
        </div>
        <div class="text-sm text-ink-700 md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Color</span
          >
          <span>{{ colorLabel(magazine) }}</span>
        </div>
        <div class="md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >State</span
          >
          <span
            class="inline-flex items-center gap-[5px] rounded border px-2 py-0.5 text-xs font-medium"
            :class="{
              'border-[#b9d9c8] bg-[#edf7f1] text-[#276746]': magazine.display_status === 'in_gun',
              'border-[#e3d3a3] bg-[#f4ecd6] text-[#7d6320]': magazine.display_status === 'loaded',
              'border-line bg-ink-50 text-ink-600': magazine.display_status === 'empty',
            }"
          >
            <span
              v-if="magazine.display_status === 'in_gun'"
              data-testid="magazine-state-marker-in_gun"
              class="h-[11px] w-[11px] shrink-0 rounded-full bg-[#2f7d57]"
            />
            <span
              v-else-if="magazine.display_status === 'loaded'"
              data-testid="magazine-state-marker-loaded"
              class="h-[11px] w-[11px] shrink-0 rounded-full bg-[#c2a14d]"
            />
            <span
              v-else-if="magazine.display_status === 'empty'"
              data-testid="magazine-state-marker-empty"
              class="h-[11px] w-[11px] shrink-0 rounded-full border-[1.5px] border-[#b6bcc1]"
            />
            {{ stateLabels[magazine.display_status] ?? magazine.display_status }}
          </span>
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
        <div v-if="!bulkMode" class="flex items-center gap-1 md:justify-end md:px-4 md:py-3">
          <router-link
            :to="{ name: 'MagazinesShow', params: { magazine_id: magazine.id } }"
            class="rounded p-1.5 text-muted transition-colors hover:bg-ink-50 hover:text-ink-900"
            title="View details"
            aria-label="View details"
          >
            <Eye class="h-4 w-4" />
          </router-link>
          <button
            v-if="magazine.lifecycle_status !== 'archived'"
            type="button"
            class="rounded p-1.5 text-brass-800 transition-colors hover:bg-brass-50"
            title="Manage state"
            aria-label="Manage state"
            @click="emit('change-state', magazine)"
          >
            <Settings2 class="h-4 w-4" />
          </button>
          <router-link
            v-if="magazine.lifecycle_status !== 'archived'"
            :to="{ name: 'MagazinesEdit', params: { magazine_id: magazine.id } }"
            class="rounded p-1.5 text-muted transition-colors hover:bg-ink-50 hover:text-ink-900"
            title="Edit magazine"
            aria-label="Edit magazine"
          >
            <Pencil class="h-4 w-4" />
          </router-link>
        </div>
      </div>
    </template>
  </div>
</template>
