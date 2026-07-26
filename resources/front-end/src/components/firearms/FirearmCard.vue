<template>
  <div
    class="flex cursor-pointer flex-col overflow-hidden rounded border border-line bg-surface transition-colors duration-150 hover:border-ink-300"
    @click="router.push({ name: 'FirearmsShow', params: { firearm_id: firearm.id } })"
  >
    <!-- Photo -->
    <div class="relative w-full overflow-hidden bg-ink-100">
      <ModelPhoto
        :src="firearm.primary_photo_card_url || firearm.primary_photo_url"
        :alt="firearm.label"
        model-type="firearm"
        family="primary"
      />
      <span
        v-if="firearm.status === 'archived'"
        class="absolute left-2.5 top-2.5 inline-flex items-center gap-1.5 rounded border border-[#dfc98d] bg-[#fbf6e8] px-2 py-1 font-mono text-[10px] font-semibold uppercase tracking-[0.05em] text-[#7d6320]"
      >
        <Archive class="h-3 w-3" />
        Archived · {{ reasonLabel }}
      </span>
    </div>

    <!-- Body -->
    <div class="flex flex-1 flex-col gap-2 px-4 py-[14px]">
      <div class="flex flex-col gap-0.5">
        <router-link
          :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
          class="inline rounded-sm font-display text-[19px] font-semibold leading-[1.1] text-brass-800 transition-colors hover:text-[#5f4b18] visited:text-brass-800 focus-visible:text-[#5f4b18]"
          @click.stop
        >
          {{ firearm.label }}
        </router-link>
        <span class="text-[13px] text-[#6b7077]">
          {{ firearm.manufacturer }} · {{ firearm.model
          }}<template v-if="firearm.type_label"> · {{ firearm.type_label }}</template
          ><template v-if="firearm.color?.label"> · {{ firearm.color.label }}</template>
        </span>
        <span v-if="firearm.customizer" class="text-[12px] text-[#6b7077]">
          Customized by {{ firearm.customizer }}
        </span>
        <span
          v-if="firearm.status === 'archived' && firearm.archive_description"
          class="mt-1 line-clamp-2 text-[12px] text-[#7d6320]"
        >
          {{ firearm.archive_description }}
        </span>
      </div>

      <div class="flex flex-wrap gap-1.5">
        <span
          v-for="caliber in firearm.calibers"
          :key="caliber.id"
          class="rounded border border-[#c2c6ca] bg-ink-50 px-[9px] py-[1px] text-[12px] text-ink-700"
          >{{ caliber.label }}</span
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
      </div>

      <div class="mt-auto flex items-center gap-1.5 pt-1 text-[14px] text-ink-500">
        <MapPin class="h-[15px] w-[15px] shrink-0 text-ink-400" />
        <span>{{ firearm.location?.full_label ?? firearm.location?.label ?? '—' }}</span>
      </div>
    </div>

    <!-- Footer -->
    <div
      class="flex items-center justify-between border-t border-[#eef0f1] bg-[#fafbfb] px-4 py-[11px]"
    >
      <div>
        <div class="font-mono text-[22px] font-medium leading-none">
          {{ formatQuantity(firearm.rounds_fired) }}
        </div>
        <div class="mt-0.5 font-mono text-[9px] tracking-[0.08em] text-muted">RNDS FIRED</div>
      </div>
      <div class="flex items-center gap-2">
        <button
          v-if="firearm.status !== 'archived'"
          type="button"
          class="inline-flex items-center gap-[5px] rounded border border-line bg-white px-[11px] py-[5px] text-[13px] font-semibold text-ink-700 transition-colors hover:bg-ink-50"
          :aria-label="`Edit ${firearm.label}`"
          @click.stop="router.push({ name: 'FirearmsEdit', params: { firearm_id: firearm.id } })"
        >
          <Pencil class="h-[13px] w-[13px]" />Edit
        </button>
        <button
          type="button"
          class="inline-flex items-center gap-[5px] rounded border border-brass-300 bg-brass-200 px-[11px] py-[5px] text-[13px] font-semibold text-brass-800 transition-colors hover:bg-[#efe2c2]"
          @click.stop="router.push({ name: 'FirearmsShow', params: { firearm_id: firearm.id } })"
        >
          <Plus v-if="firearm.status !== 'archived'" class="h-[13px] w-[13px]" />
          {{ firearm.status === 'archived' ? 'View' : 'Log' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Archive, MapPin, Pencil, Plus } from 'lucide-vue-next';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';
import { useRouter } from 'vue-router';
import { useNumbers } from '@/composables/useNumbers';

const router = useRouter();
const { formatQuantity } = useNumbers();

const props = defineProps({
  firearm: { type: Object, required: true },
});

const reasonLabel = computed(() =>
  String(props.firearm.archive_reason ?? 'archived')
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (character) => character.toUpperCase())
);

function accessoryBadge(type) {
  return {
    Suppressor: 'SUPPR',
    Optic: 'OPTIC',
    Light: 'LIGHT',
    Misc: 'MISC',
  }[type];
}
</script>
