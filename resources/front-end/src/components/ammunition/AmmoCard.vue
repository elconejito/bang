<template>
  <div
    class="flex cursor-pointer flex-col overflow-hidden rounded border bg-surface transition-[border-color,box-shadow] duration-150"
    :class="
      isLow
        ? 'border-[#e0a999] hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]'
        : 'border-line hover:border-ink-300 hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_8px_20px_rgba(20,22,26,0.07)]'
    "
    @click="router.push({ name: 'AmmoShow', params: { ammunition_id: ammo.id } })"
  >
    <!-- Body -->
    <div class="flex flex-1 flex-col gap-[9px] p-4">
      <div class="flex items-start justify-between gap-2">
        <span class="text-[13px] text-muted">{{ ammo.manufacturer }}</span>
        <span
          v-if="isLow"
          class="inline-flex shrink-0 items-center gap-1 rounded border border-[#e0a999] bg-[#f7e9e4] px-1.5 py-px font-mono text-[10px] text-[#b4452f]"
        >
          <TriangleAlert class="h-[11px] w-[11px]" />LOW
        </span>
      </div>
      <span class="font-display text-[17px] font-semibold leading-[1.15]">{{ loadTitle }}</span>
      <div class="mt-auto flex items-center gap-2">
        <span
          v-if="ammo.purpose"
          class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[9px] py-px text-[12px] text-ink-700"
          >{{ ammo.purpose.label }}</span
        >
      </div>
    </div>

    <!-- Footer -->
    <div
      class="flex items-center justify-between px-4 py-[11px]"
      :class="
        isLow ? 'border-t border-[#f1e2dc] bg-[#fdf6f3]' : 'border-t border-[#eef0f1] bg-[#fafbfb]'
      "
    >
      <div>
        <div
          class="font-mono text-[22px] font-medium leading-none"
          :class="isLow ? 'text-[#b4452f]' : ''"
        >
          {{ ammo.on_hand.toLocaleString() }}
        </div>
        <div class="mt-[2px] font-mono text-[9px] uppercase tracking-[0.07em] text-muted">
          <template v-if="isLow && ammo.reorder_min != null"
            >ON HAND · MIN {{ ammo.reorder_min.toLocaleString() }}</template
          >
          <template v-else>RNDS ON HAND</template>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-[5px] rounded border border-line bg-white px-[11px] py-[5px] text-[13px] font-semibold text-ink-700 transition-colors hover:bg-ink-50"
          :aria-label="`Edit ${ammo.label}`"
          @click.stop="router.push({ name: 'AmmoEdit', params: { ammunition_id: ammo.id } })"
        >
          <Pencil class="h-[13px] w-[13px]" />Edit
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-[5px] rounded border px-[11px] py-[5px] text-[13px] font-semibold transition-colors"
          :class="
            isLow
              ? 'border-[#e0a999] bg-white text-[#b4452f] hover:bg-[#fbeee9]'
              : 'border-[#e3d3a3] bg-[#f4ecd6] text-[#7d6320] hover:bg-[#efe2c2]'
          "
          @click.stop="$emit('add-stock', ammo)"
        >
          <Plus class="h-[13px] w-[13px]" />Stock
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { Pencil, Plus, TriangleAlert } from 'lucide-vue-next';

const props = defineProps({
  ammo: { type: Object, required: true },
});

defineEmits(['add-stock']);

const router = useRouter();

const isLow = computed(() =>
  props.ammo.reorder_min != null
    ? props.ammo.on_hand <= props.ammo.reorder_min
    : props.ammo.on_hand === 0
);

const loadTitle = computed(() =>
  [
    props.ammo.label,
    props.ammo.weight ? `${props.ammo.weight}gr` : null,
    props.ammo.bullet_type?.label,
  ]
    .filter(Boolean)
    .join(' · ')
);
</script>
