<template>
  <div v-if="isLoading" class="h-48 animate-pulse rounded border border-line bg-ink-50" />

  <EmptyState
    v-else-if="firearms.length === 0"
    :title="emptyTitle"
    :message="emptyMessage"
    :action-label="emptyActionLabel"
    :action-to="emptyActionTo"
  />

  <div v-else class="overflow-hidden rounded border border-line bg-white">
    <div
      class="hidden grid-cols-[minmax(220px,1.5fr)_minmax(120px,0.8fr)_minmax(140px,1fr)_minmax(120px,0.8fr)_110px_150px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
    >
      <div class="px-4 py-2.5">Firearm</div>
      <div class="px-3 py-2.5">Caliber</div>
      <div class="px-3 py-2.5">Accessories</div>
      <div class="px-3 py-2.5">Storage</div>
      <div class="px-3 py-2.5 text-right">Rounds Fired</div>
      <div class="px-4 py-2.5 text-right">Actions</div>
    </div>

    <div
      v-for="firearm in firearms"
      :key="firearm.id"
      class="grid cursor-pointer gap-3 border-b border-ink-100 px-4 py-4 transition-colors last:border-b-0 hover:bg-[#fafbfb] md:grid-cols-[minmax(220px,1.5fr)_minmax(120px,0.8fr)_minmax(140px,1fr)_minmax(120px,0.8fr)_110px_150px] md:items-center md:gap-0 md:px-0 md:py-0"
      role="link"
      tabindex="0"
      @click="showFirearm(firearm)"
      @keydown.enter="showFirearm(firearm)"
      @keydown.space.prevent="showFirearm(firearm)"
    >
      <div class="min-w-0 md:px-4 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Firearm</span
        >
        <div class="flex min-w-0 items-center gap-2">
          <router-link
            :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
            class="min-w-0 truncate rounded-sm font-display text-[16px] font-semibold text-brass-800 transition-colors hover:text-[#5f4b18] visited:text-brass-800 focus-visible:text-[#5f4b18]"
            @click.stop
          >
            {{ firearm.label }}
          </router-link>
          <span
            v-if="firearm.status === 'archived'"
            class="shrink-0 rounded border border-[#dfc98d] bg-[#fbf6e8] px-1.5 py-0.5 font-mono text-[9px] font-semibold uppercase tracking-[0.04em] text-[#7d6320]"
          >
            Archived · {{ reasonLabel(firearm.archive_reason) }}
          </span>
        </div>
        <div class="truncate text-[12px] text-muted">
          {{ firearm.manufacturer }} <span aria-hidden="true">&middot;</span> {{ firearm.model }}
        </div>
        <div v-if="firearm.customizer" class="truncate text-[11px] text-muted">
          Customized by {{ firearm.customizer }}
        </div>
      </div>

      <div class="flex flex-wrap gap-1.5 md:px-3 md:py-3">
        <span class="mb-1 w-full font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Caliber</span
        >
        <span
          v-for="caliber in firearm.calibers"
          :key="caliber.id"
          class="rounded border border-[#c2c6ca] bg-ink-50 px-[9px] py-[1px] text-[12px] text-ink-700"
        >
          {{ caliber.label }}
        </span>
        <span v-if="!firearm.calibers?.length" class="text-[13px] text-muted">&mdash;</span>
      </div>

      <div class="flex flex-wrap gap-1.5 md:px-3 md:py-3">
        <span class="mb-1 w-full font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Accessories</span
        >
        <span
          v-for="accessory in firearm.mounted_accessories"
          :key="`${accessory.type}-${accessory.id}`"
          :title="accessory.label"
          class="rounded border px-[7px] py-[2px] font-mono text-[10px] tracking-[0.04em] whitespace-nowrap"
          :class="
            accessory.type === 'Suppressor'
              ? 'border-special-border bg-special-bg text-special'
              : 'border-[#d6d9dc] text-muted'
          "
        >
          {{ accessoryBadge(accessory.type) }}
        </span>
        <span v-if="!firearm.mounted_accessories?.length" class="text-[13px] text-muted"
          >&mdash;</span
        >
      </div>

      <div class="text-[13px] text-ink-700 md:px-3 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Storage</span
        >
        <template v-if="firearm.location?.label">{{ firearm.location.label }}</template>
        <template v-else>&mdash;</template>
      </div>

      <div class="font-mono text-[14px] text-ink-900 md:px-3 md:py-3 md:text-right">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Rounds Fired</span
        >
        {{ formatQuantity(firearm.rounds_fired) }}
      </div>

      <div class="flex items-center gap-2 md:justify-end md:px-4 md:py-3">
        <button
          v-if="firearm.status !== 'archived'"
          type="button"
          class="inline-flex items-center gap-[5px] rounded border border-line bg-white px-[9px] py-[5px] text-[13px] font-semibold text-ink-700 transition-colors hover:bg-ink-50"
          :aria-label="`Edit ${firearm.label}`"
          @click.stop="editFirearm(firearm)"
        >
          <Pencil class="h-[13px] w-[13px]" />Edit
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-[5px] rounded border border-brass-300 bg-brass-200 px-[11px] py-[5px] text-[13px] font-semibold text-brass-800 transition-colors hover:bg-[#efe2c2]"
          :aria-label="`${firearm.status === 'archived' ? 'View' : 'Log activity for'} ${firearm.label}`"
          @click.stop="showFirearm(firearm)"
        >
          <Plus v-if="firearm.status !== 'archived'" class="h-[13px] w-[13px]" />
          {{ firearm.status === 'archived' ? 'View' : 'Log' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Pencil, Plus } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import { useNumbers } from '@/composables/useNumbers';
import EmptyState from '@/components/EmptyState.vue';

defineProps({
  firearms: { type: Array, required: true },
  isLoading: { type: Boolean, default: false },
  emptyTitle: { type: String, default: 'No firearms found' },
  emptyMessage: { type: String, default: 'Try adjusting your search or filters.' },
  emptyActionLabel: { type: String, default: '' },
  emptyActionTo: { type: [String, Object], default: null },
});

const router = useRouter();
const { formatQuantity } = useNumbers();

function accessoryBadge(type) {
  return {
    Suppressor: 'SUPPR',
    Optic: 'OPTIC',
    Light: 'LIGHT',
    Misc: 'MISC',
  }[type];
}

function showFirearm(firearm) {
  router.push({ name: 'FirearmsShow', params: { firearm_id: firearm.id } });
}

function editFirearm(firearm) {
  router.push({ name: 'FirearmsEdit', params: { firearm_id: firearm.id } });
}

function reasonLabel(reason) {
  return String(reason ?? 'archived')
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (character) => character.toUpperCase());
}
</script>
