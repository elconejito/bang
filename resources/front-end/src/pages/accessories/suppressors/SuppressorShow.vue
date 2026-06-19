<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { Check, Clock } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import { useSuppressorsStore } from '@/stores/suppressors';
import { useNumbers } from '@/composables/useNumbers';
import dayjs from 'dayjs';

const props = defineProps({
  suppressorId: { type: Number, required: true },
});

const router = useRouter();
const suppressorsStore = useSuppressorsStore();
const { formatQuantity } = useNumbers();

const suppressor = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await suppressorsStore.fetchOne(props.suppressorId);
  suppressor.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Suppressors' },
  { label: suppressor.value?.label ?? '…' },
]);
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else-if="suppressor">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2.5 mb-1">
            <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">{{ suppressor.label }}</h1>
            <span v-if="suppressor.is_nfa" class="font-mono text-[11px] text-white bg-[#1a1c1f] rounded-sm px-[7px] py-[2px]">NFA</span>
          </div>
          <div class="text-[15px] text-[#6b7077]">
            {{ suppressor.manufacturer }} · Suppressor
            <template v-if="suppressor.caliber"> · {{ suppressor.caliber.label }}</template>
          </div>
        </div>
        <div class="flex items-center gap-2.5 ml-auto">
          <router-link
            :to="{ name: 'SuppressorEdit', params: { suppressor_id: suppressor.id } }"
            class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 rounded border border-[#c2c6ca] hover:bg-[#f5f6f7] transition-colors"
          >
            <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            Edit
          </router-link>
        </div>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">

        <!-- Left rail -->
        <div class="flex flex-col gap-4">
          <!-- Photo stub -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="h-[150px] bg-[#f5f6f7] flex items-center justify-center text-muted text-[13px]">No photo</div>
          </div>

          <!-- Mounted status -->
          <div
            :class="suppressor.firearm_id ? 'bg-[#e7f1eb] border-[#9ccbb1]' : 'bg-[#f5f6f7] border-[#c2c6ca]'"
            class="border rounded-sm p-[13px_16px] flex items-center gap-3"
          >
            <div :class="suppressor.firearm_id ? 'border-[#9ccbb1] text-[#2f7d57]' : 'border-[#c2c6ca] text-[#5b6066]'" class="w-9 h-9 rounded-sm bg-white border flex items-center justify-center">
              <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div :class="suppressor.firearm_id ? 'text-[#2f7d57]' : 'text-[#5b6066]'" class="font-mono text-[10px] tracking-[0.06em]">
                {{ suppressor.firearm_id ? 'MOUNTED ON' : 'UNMOUNTED' }}
              </div>
              <div class="text-[16px] font-semibold">
                {{ suppressor.firearm ? (suppressor.firearm.label ?? suppressor.firearm.manufacturer) : (suppressor.location?.label ?? 'No location') }}
              </div>
            </div>
          </div>

          <!-- Rounds through can -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="flex items-baseline justify-between border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-[14px]">
              <span class="text-[14px] text-[#6b7077]">Rounds through can</span>
              <span class="font-mono text-[28px] font-medium leading-none">{{ formatQuantity(suppressor.rounds_fired ?? 0) }}</span>
            </div>
            <div class="px-4 py-1.5">
              <div v-if="suppressor.caliber" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]">
                <span class="text-[14px] text-[#6b7077]">Caliber rating</span>
                <span class="rounded border border-[#c2c6ca] bg-[#f5f6f7] px-[9px] py-px text-[12px] text-[#3a3e44]">{{ suppressor.caliber.label }}</span>
              </div>
              <div v-if="suppressor.mount_type" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Mount type</span>
                <span class="text-[14px]">{{ suppressor.mount_type }}</span>
              </div>
            </div>
          </div>

          <!-- Specs -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">Specs</div>
            <div class="px-4 py-1.5">
              <div v-if="suppressor.serial" class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]">
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ suppressor.serial }}</span>
              </div>
              <div v-if="suppressor.purchase_date" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Purchased</span>
                <span class="text-[14px]">
                  {{ dayjs(suppressor.purchase_date).format('MMM YYYY') }}<template v-if="suppressor.purchase_price"> · <span class="font-mono">${{ Number(suppressor.purchase_price).toLocaleString() }}</span></template>
                </span>
              </div>
            </div>
          </div>

          <!-- NFA record -->
          <div v-if="suppressor.is_nfa" class="bg-white border border-[#ddd4ea] rounded-sm overflow-hidden">
            <div class="flex items-center gap-2 border-b border-[#eee9f3] bg-[#f7f4fa] px-4 py-3">
              <svg class="h-[15px] w-[15px] text-[#6b5a8c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span class="font-display font-semibold text-[16px] text-[#4a3d63]">NFA record</span>
            </div>
            <div class="px-4 py-1.5">
              <div v-if="suppressor.serial" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Serial #</span>
                <span class="font-mono text-[14px]">{{ suppressor.serial }}</span>
              </div>
              <div class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Tax stamp</span>
                <span v-if="suppressor.nfa_approved_date" class="inline-flex items-center gap-[5px] text-[13px] font-semibold text-[#2f7d57]">
                  <Check class="h-[13px] w-[13px]" />Approved
                </span>
                <span v-else class="inline-flex items-center gap-[5px] text-[13px] text-[#8a9098]">
                  <Clock class="h-[13px] w-[13px]" />Pending
                </span>
              </div>
              <div v-if="suppressor.nfa_approved_date || suppressor.nfa_form_type" class="flex items-center justify-between border-b border-[#f1f2f3] py-[9px]">
                <span class="text-[14px] text-[#6b7077]">{{ suppressor.nfa_form_type ? suppressor.nfa_form_type + ' cleared' : 'Form cleared' }}</span>
                <span class="text-[14px]">{{ suppressor.nfa_approved_date ? dayjs(suppressor.nfa_approved_date).format('MMM YYYY') : '—' }}</span>
              </div>
              <div v-if="suppressor.nfa_trust" class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Trust</span>
                <span class="text-[14px]">{{ suppressor.nfa_trust }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: History (stubbed) -->
        <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
          <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
            <span class="font-display font-semibold text-[18px]">History</span>
            <span class="font-mono text-[11px] text-muted tracking-[0.04em]">MOUNTS · ROUNDS · MAINTENANCE</span>
          </div>
          <div class="px-[18px] py-12 text-center text-muted text-[14px]">
            History timeline coming soon.
          </div>
        </div>

      </div>
    </template>
  </div>
</template>
