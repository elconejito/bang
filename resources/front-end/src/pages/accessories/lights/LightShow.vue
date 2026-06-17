<script setup>
import { ref, computed, onMounted } from 'vue';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import { useLightsStore } from '@/stores/lights';
import dayjs from 'dayjs';

const props = defineProps({
  lightId: { type: Number, required: true },
});

const lightsStore = useLightsStore();
const light = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await lightsStore.fetchOne(props.lightId);
  light.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Lights' },
  { label: light.value?.label ?? '…' },
]);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>
    <template v-else-if="light">
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">{{ light.label }}</h1>
          <div class="text-[15px] text-[#6b7077]">{{ light.manufacturer }} · Light<template v-if="light.lumens"> · {{ light.lumens.toLocaleString() }} lm</template></div>
        </div>
        <router-link :to="{ name: 'LightEdit', params: { light_id: light.id } }" class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 rounded border border-[#c2c6ca] hover:bg-[#f5f6f7] transition-colors">
          <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          Edit
        </router-link>
      </div>
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <div class="flex flex-col gap-4">
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="h-[150px] bg-[#f5f6f7] flex items-center justify-center text-muted text-[13px]">No photo</div>
          </div>
          <div :class="light.firearm_id ? 'bg-[#e7f1eb] border-[#9ccbb1]' : 'bg-[#f5f6f7] border-[#c2c6ca]'" class="border rounded-sm p-[13px_16px] flex items-center gap-3">
            <div :class="light.firearm_id ? 'border-[#9ccbb1] text-[#2f7d57]' : 'border-[#c2c6ca] text-[#5b6066]'" class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center">
              <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div :class="light.firearm_id ? 'text-[#2f7d57]' : 'text-[#5b6066]'" class="font-mono text-[10px] tracking-[0.06em]">{{ light.firearm_id ? 'MOUNTED ON' : 'UNMOUNTED' }}</div>
              <div class="text-[16px] font-semibold">{{ light.firearm ? (light.firearm.label ?? light.firearm.manufacturer) : (light.location?.label ?? 'No location') }}</div>
            </div>
          </div>
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Specs</div>
            <div class="px-4 py-1.5">
              <div v-if="light.lumens" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"><span class="text-[14px] text-[#6b7077]">Lumens</span><span class="text-[14px]">{{ light.lumens.toLocaleString() }} lm</span></div>
              <div v-if="light.battery_type" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"><span class="text-[14px] text-[#6b7077]">Battery</span><span class="text-[14px]">{{ light.battery_type }}</span></div>
              <div v-if="light.serial" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]"><span class="text-[14px] text-[#6b7077]">Serial #</span><span class="font-mono text-[14px]">{{ light.serial }}</span></div>
              <div v-if="light.purchase_date" class="flex items-center justify-between py-[9px]"><span class="text-[14px] text-[#6b7077]">Purchased</span><span class="text-[14px]">{{ dayjs(light.purchase_date).format('MMM YYYY') }}<template v-if="light.purchase_price"> · <span class="font-mono">${{ Number(light.purchase_price).toLocaleString() }}</span></template></span></div>
            </div>
          </div>
        </div>
        <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
          <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
            <span class="font-display font-semibold text-[18px]">History</span>
            <span class="font-mono text-[11px] text-muted tracking-[0.04em]">MOUNTS · MAINTENANCE</span>
          </div>
          <div class="px-[18px] py-12 text-center text-muted text-[14px]">History timeline coming soon.</div>
        </div>
      </div>
    </template>
  </div>
</template>
