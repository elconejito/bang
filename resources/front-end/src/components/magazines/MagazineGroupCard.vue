<script setup>
import { computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';
import { useRoute } from 'vue-router';

const props = defineProps({
  group: { type: Object, required: true },
});
const route = useRoute();

const total = computed(() => props.group.summary.total);
const segments = computed(() =>
  ['in_gun', 'loaded', 'empty'].map((state) => ({
    state,
    count: props.group.summary[state],
    width: total.value ? (props.group.summary[state] / total.value) * 100 : 0,
  }))
);
const destination = computed(() => ({
  name: 'MagazineGroupShow',
  params: { group: props.group.key },
  query: route.query.lifecycle_status
    ? { lifecycle_status: route.query.lifecycle_status }
    : undefined,
}));
</script>

<template>
  <router-link
    :to="destination"
    class="block overflow-hidden rounded border border-line bg-white transition hover:border-[#c2c6ca] hover:shadow-md"
  >
    <div class="flex flex-wrap items-center gap-4 px-[18px] py-4">
      <div class="min-w-[180px] flex-1">
        <h2 class="font-display text-[18px] font-semibold text-ink-900">
          {{ group.model_name || 'Magazine' }}
        </h2>
        <p class="text-[13px] text-muted">
          {{ group.manufacturer }} ·
          {{ group.calibers.map((caliber) => caliber.label).join(' / ') }} · {{ group.capacity }} rd
        </p>
      </div>

      <div class="min-w-[220px] flex-[2]">
        <div class="flex h-2.5 overflow-hidden rounded-full border border-[#d6d9dc]">
          <div
            v-for="segment in segments"
            :key="segment.state"
            :style="{ width: `${segment.width}%` }"
            :class="{
              'bg-[#2f7d57]': segment.state === 'in_gun',
              'bg-[#c2a14d]': segment.state === 'loaded',
              'bg-[#eceef0]': segment.state === 'empty',
            }"
          />
        </div>
        <div class="mt-2 flex flex-wrap gap-3 text-[12px] text-[#5b6066]">
          <span>{{ group.summary.in_gun }} in firearm</span>
          <span>{{ group.summary.loaded }} loaded</span>
          <span>{{ group.summary.empty }} empty</span>
        </div>
      </div>

      <div class="ml-auto flex items-center gap-2 text-brass-800">
        <span class="font-mono text-[11px]">{{ total }} MAGS</span>
        <ChevronRight class="h-4 w-4" />
      </div>
    </div>
  </router-link>
</template>
