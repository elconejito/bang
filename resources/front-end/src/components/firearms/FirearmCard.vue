<template>
  <div
    class="flex cursor-pointer flex-col overflow-hidden rounded border border-line bg-surface transition-all hover:border-ink-300 hover:shadow-[0_1px_2px_rgba(20,22,26,0.05),0_10px_24px_rgba(20,22,26,0.08)]"
    @click="router.push({ name: 'FirearmsShow', params: { firearm_id: firearm.id } })"
  >
    <!-- Photo -->
    <div class="relative h-[172px] w-full overflow-hidden bg-ink-100">
      <img
        v-if="firearm.primary_photo_url"
        :src="firearm.primary_photo_url"
        :alt="firearm.label"
        class="h-full w-full object-cover"
      />
      <div v-else class="flex h-full w-full items-center justify-center">
        <ImageIcon class="h-8 w-8 text-ink-300" />
      </div>
    </div>

    <!-- Body -->
    <div class="flex flex-1 flex-col gap-2 px-4 py-[14px]">
      <div class="flex flex-col gap-0.5">
        <span class="font-display text-[19px] font-semibold leading-[1.1]">{{
          firearm.label
        }}</span>
        <span class="text-[13px] text-[#6b7077]"
          >{{ firearm.manufacturer }} · {{ firearm.model }}</span
        >
      </div>

      <div class="flex flex-wrap gap-1.5">
        <span
          v-for="caliber in firearm.calibers"
          :key="caliber.id"
          class="rounded border border-[#c2c6ca] bg-ink-50 px-[9px] py-[1px] text-[12px] text-ink-700"
          >{{ caliber.label }}</span
        >
      </div>

      <div class="mt-auto flex items-center gap-1.5 pt-1 text-[14px] text-ink-500">
        <MapPin class="h-[15px] w-[15px] shrink-0 text-ink-400" />
        <span>{{ firearm.location?.label ?? '—' }}</span>
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
      <button
        class="inline-flex items-center gap-[5px] rounded border border-brass-300 bg-brass-200 px-[11px] py-[5px] text-[13px] font-semibold text-brass-800 transition-colors hover:bg-[#efe2c2]"
        @click.stop="router.push({ name: 'FirearmsShow', params: { firearm_id: firearm.id } })"
      >
        <Plus class="h-[13px] w-[13px]" />
        Log
      </button>
    </div>
  </div>
</template>

<script setup>
import { Image as ImageIcon, MapPin, Plus } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import { useNumbers } from '@/composables/useNumbers';

const router = useRouter();
const { formatQuantity } = useNumbers();

defineProps({
  firearm: { type: Object, required: true },
});
</script>
