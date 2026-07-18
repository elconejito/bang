<template>
  <div v-for="group in groups" :key="group.caliberLabel" class="mb-8">
    <div class="mb-4 flex flex-wrap items-baseline gap-3 border-b border-[#d6d9dc] pb-2">
      <span class="font-display text-[22px] font-bold tracking-[-0.01em]">{{
        group.caliberLabel
      }}</span>
      <span
        v-if="group.isLow"
        class="rounded border border-caution-border bg-caution-bg px-1.5 py-0.5 font-mono text-[10px] tracking-[0.04em] text-caution"
        >LOW</span
      >
      <span class="font-mono text-[12px] tracking-[0.03em] text-muted">
        {{ group.totalRounds.toLocaleString() }} ON HAND · {{ group.items.length }} LOADS
      </span>
    </div>

    <div class="overflow-hidden rounded border border-line bg-white">
      <div
        class="hidden grid-cols-[minmax(220px,1.5fr)_minmax(120px,0.8fr)_140px_110px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
      >
        <div class="px-4 py-2.5">Load</div>
        <div class="px-3 py-2.5">Purpose</div>
        <div class="px-3 py-2.5 text-right">On Hand</div>
        <div class="px-4 py-2.5 text-right">Actions</div>
      </div>

      <div
        v-for="ammo in group.items"
        :key="ammo.id"
        class="grid cursor-pointer gap-3 border-b border-ink-100 px-4 py-4 transition-colors hover:bg-[#fafbfb] md:grid-cols-[minmax(220px,1.5fr)_minmax(120px,0.8fr)_140px_110px] md:items-center md:gap-0 md:px-0 md:py-0"
        role="link"
        tabindex="0"
        @click="showAmmo(ammo)"
        @keydown.enter="showAmmo(ammo)"
        @keydown.space.prevent="showAmmo(ammo)"
      >
        <div class="min-w-0 md:px-4 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Load</span
          >
          <div class="flex min-w-0 items-center gap-2">
            <span class="truncate font-display text-[16px] font-semibold text-ink-900">
              {{ ammo.label }}
            </span>
            <span
              v-if="isLow(ammo)"
              class="shrink-0 rounded border border-[#e0a999] bg-[#f7e9e4] px-1.5 py-px font-mono text-[10px] text-[#b4452f]"
            >
              LOW
            </span>
          </div>
          <div class="truncate text-[12px] text-muted">{{ ammo.manufacturer }}</div>
        </div>
        <div class="text-[13px] text-ink-700 md:px-3 md:py-3">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >Purpose</span
          >
          <span
            v-if="ammo.purpose"
            class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[9px] py-px text-[12px] text-ink-700"
            >{{ ammo.purpose.label }}</span
          >
          <span v-else class="text-muted">—</span>
        </div>
        <div class="md:px-3 md:py-3 md:text-right">
          <span
            class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
            >On Hand</span
          >
          <div
            class="font-mono text-[17px] font-medium"
            :class="isLow(ammo) ? 'text-[#b4452f]' : 'text-ink-900'"
          >
            {{ ammo.on_hand.toLocaleString() }}
          </div>
          <div
            v-if="isLow(ammo) && ammo.reorder_min != null"
            class="font-mono text-[9px] uppercase tracking-[0.07em] text-muted"
          >
            MIN {{ ammo.reorder_min.toLocaleString() }}
          </div>
        </div>
        <div class="flex items-center md:justify-end md:px-4 md:py-3">
          <button
            type="button"
            class="inline-flex items-center gap-[5px] rounded border px-[11px] py-[5px] text-[13px] font-semibold transition-colors"
            :class="
              isLow(ammo)
                ? 'border-[#e0a999] bg-white text-[#b4452f] hover:bg-[#fbeee9]'
                : 'border-[#e3d3a3] bg-[#f4ecd6] text-[#7d6320] hover:bg-[#efe2c2]'
            "
            :aria-label="`Add stock for ${ammo.label}`"
            @click.stop="$emit('add-stock', ammo)"
          >
            <Plus class="h-[13px] w-[13px]" />Stock
          </button>
        </div>
      </div>
      <router-link
        :to="{ name: 'AmmoCreate', query: { caliber_id: group.caliberId } }"
        class="flex items-center justify-center gap-[7px] bg-[#fafbfb] px-4 py-3 text-[14px] text-muted transition-colors hover:bg-[#f3f4f5]"
      >
        <Plus class="h-[17px] w-[17px] text-[#7d6320]" /> Add a {{ group.caliberLabel }} load
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { Plus } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

defineProps({ groups: { type: Array, required: true } });
defineEmits(['add-stock']);
const router = useRouter();
function isLow(ammo) {
  return ammo.reorder_min != null ? ammo.on_hand <= ammo.reorder_min : ammo.on_hand === 0;
}
function showAmmo(ammo) {
  router.push({ name: 'AmmoShow', params: { ammunition_id: ammo.id } });
}
</script>
