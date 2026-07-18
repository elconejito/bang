<script setup>
import { computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  items: { type: Array, required: true },
  type: {
    type: String,
    required: true,
    validator: (value) => ['suppressors', 'magazines', 'optics', 'lights', 'misc'].includes(value),
  },
  addRoute: { type: String, default: null },
  addLabel: { type: String, default: null },
});

const FITS_TYPES = ['holster', 'case', 'bag'];

const typeLabel = computed(
  () =>
    ({
      suppressors: 'Suppressor',
      magazines: 'Magazine',
      optics: 'Optic',
      lights: 'Light',
      misc: 'Misc',
    })[props.type]
);

function showRoute(item) {
  if (props.type === 'magazines') {
    return { name: 'MagazineGroupShow', params: { group: item.key } };
  }

  const routes = {
    suppressors: ['SuppressorShow', 'suppressor_id'],
    optics: ['OpticShow', 'optic_id'],
    lights: ['LightShow', 'light_id'],
    misc: ['MiscShow', 'misc_id'],
  };
  const [name, param] = routes[props.type];
  return { name, params: { [param]: item.id } };
}

function editRoute(item) {
  const routes = {
    suppressors: ['SuppressorEdit', 'suppressor_id'],
    optics: ['OpticEdit', 'optic_id'],
    lights: ['LightEdit', 'light_id'],
    misc: ['MiscEdit', 'misc_id'],
  };
  const [name, param] = routes[props.type];
  return { name, params: { [param]: item.id } };
}

function detail(item) {
  if (props.type === 'magazines') {
    return [
      item.manufacturer,
      item.calibers?.map((caliber) => caliber.label).join(' / '),
      item.capacity ? `${item.capacity} rd` : null,
    ]
      .filter(Boolean)
      .join(' · ');
  }

  if (props.type === 'suppressors') {
    return [item.caliber?.label, item.mount_type].filter(Boolean).join(' · ') || '—';
  }
  if (props.type === 'optics') {
    return (
      [item.optic_type?.replaceAll('_', ' '), item.battery_type].filter(Boolean).join(' · ') || '—'
    );
  }
  if (props.type === 'lights') {
    return (
      [item.lumens ? `${item.lumens.toLocaleString()} lm` : null, item.battery_type]
        .filter(Boolean)
        .join(' · ') || '—'
    );
  }
  return item.sub_type ? item.sub_type.replaceAll('_', ' ') : '—';
}

function status(item) {
  if (item.status === 'archived') {
    return `ARCHIVED · ${String(item.archive_reason ?? 'other').replaceAll('_', ' ')}`;
  }
  if (props.type === 'magazines') {
    return `${item.summary.in_gun} in firearm · ${item.summary.loaded} loaded · ${item.summary.empty} empty`;
  }
  if (item.firearm) {
    return `ON · ${item.firearm.label ?? item.firearm.manufacturer}`;
  }
  if (props.type === 'misc' && FITS_TYPES.includes(item.sub_type?.toLowerCase())) {
    return `FITS · ${item.location?.label ?? 'Unassigned'}`;
  }
  return `OFF · ${item.location?.label ?? 'Unmounted'}`;
}

function actionLabel(item) {
  if (item.status === 'archived') return 'View';
  if (props.type === 'magazines') return 'View';
  if (item.firearm) return 'Move';
  if (props.type === 'misc' && FITS_TYPES.includes(item.sub_type?.toLowerCase())) return 'Edit';
  return 'Mount';
}
</script>

<template>
  <div class="mb-8 overflow-hidden rounded border border-line bg-white">
    <div
      class="hidden grid-cols-[minmax(220px,1.4fr)_110px_minmax(150px,1fr)_minmax(190px,1.1fr)_90px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
    >
      <div class="px-4 py-2.5">Item</div>
      <div class="px-3 py-2.5">Type</div>
      <div class="px-3 py-2.5">Details</div>
      <div class="px-3 py-2.5">Status</div>
      <div class="px-4 py-2.5 text-right">Actions</div>
    </div>

    <router-link
      v-for="item in items"
      :key="item.key ?? item.id"
      :to="showRoute(item)"
      class="grid gap-3 border-b border-ink-100 px-4 py-4 transition-colors last:border-b-0 hover:bg-[#fafbfb] md:grid-cols-[minmax(220px,1.4fr)_110px_minmax(150px,1fr)_minmax(190px,1.1fr)_90px] md:items-center md:gap-0 md:px-0 md:py-0"
    >
      <div class="min-w-0 md:px-4 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Item</span
        >
        <div class="truncate font-display text-[16px] font-semibold text-ink-900">
          {{ type === 'magazines' ? item.model_name || 'Magazine' : item.label }}
        </div>
        <div class="truncate text-[12px] text-muted">{{ item.manufacturer || '—' }}</div>
        <div v-if="type === 'suppressors' && item.serial" class="font-mono text-[10px] text-muted">
          SN · {{ item.serial.slice(-4) }}
        </div>
      </div>

      <div class="md:px-3 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Type</span
        >
        <span
          class="rounded border border-line px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.05em] text-muted"
        >
          {{ typeLabel }}<template v-if="type === 'suppressors' && item.is_nfa"> · NFA</template>
        </span>
      </div>

      <div class="capitalize text-[13px] text-ink-700 md:px-3 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Details</span
        >
        {{ detail(item) }}
      </div>

      <div class="text-[12px] text-ink-700 md:px-3 md:py-3">
        <span class="mb-1 block font-mono text-[10px] uppercase tracking-wide text-muted md:hidden"
          >Status</span
        >
        <span
          class="inline-flex rounded border px-2 py-0.5 font-mono text-[10px]"
          :class="
            item.status === 'archived'
              ? 'border-[#dfc98d] bg-[#fbf6e8] text-[#7d6320]'
              : type !== 'magazines' && item.firearm
                ? 'border-[#9ccbb1] bg-[#e7f1eb] text-[#2f7d57]'
                : 'border-line bg-ink-50 text-muted'
          "
        >
          {{ status(item) }}
        </span>
      </div>

      <div class="flex md:justify-end md:px-4 md:py-3">
        <router-link
          :to="
            type === 'magazines' || item.status === 'archived' ? showRoute(item) : editRoute(item)
          "
          class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 hover:underline"
          @click.stop
        >
          {{ actionLabel(item) }} <ChevronRight class="h-[13px] w-[13px]" />
        </router-link>
      </div>
    </router-link>
    <router-link
      v-if="addRoute"
      :to="{ name: addRoute }"
      class="flex items-center gap-1.5 px-4 py-3 text-[14px] font-semibold text-brass-800 transition-colors hover:bg-[#fafbfb]"
    >
      + {{ addLabel }}
    </router-link>
  </div>
</template>
