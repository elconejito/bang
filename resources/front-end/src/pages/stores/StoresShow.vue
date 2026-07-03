<script setup>
import { ref, computed, onMounted } from 'vue';
import { Camera, Plus } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import { useGunStoresStore } from '@/stores/gunStores';
import dayjs from 'dayjs';

const props = defineProps({
  storeId: { type: Number, required: true },
});

const gunStoresStore = useGunStoresStore();
const store = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await gunStoresStore.fetchOne(props.storeId);
  store.value = data;
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Manage Lists', to: { name: 'ReferenceData', params: { list: 'store' } } },
  { label: store.value?.label ?? '…' },
]);

function formatCurrency(value) {
  return (
    '$' +
    Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  );
}
</script>

<template>
  <div class="max-w-[1280px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else-if="store">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6 flex-wrap">
        <div class="flex-1 min-w-0">
          <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">
            {{ store.label }}
          </h1>
          <div class="text-[15px] text-[#6b7077]">Gun Store</div>
        </div>
        <router-link
          :to="{ name: 'StoreEdit', params: { store_id: storeId } }"
          class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 rounded border border-[#c2c6ca] hover:bg-[#f5f6f7] transition-colors"
        >
          <svg
            class="w-[15px] h-[15px]"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
          </svg>
          Edit
        </router-link>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-[344px_1fr] gap-6 items-start">
        <!-- Left rail -->
        <div class="flex flex-col gap-4">
          <!-- Photo card -->
          <div class="overflow-hidden rounded border border-line bg-surface">
            <router-link
              :to="{ name: 'StoreGallery', params: { store_id: storeId } }"
              class="block"
            >
              <div class="relative h-[208px] w-full bg-ink-100">
                <img
                  v-if="store.primary_photo_url"
                  :src="store.primary_photo_url"
                  :alt="store.label"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <Camera class="h-10 w-10 text-ink-300" />
                </div>
                <span
                  class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 rounded bg-[rgba(26,28,31,0.82)] px-[10px] py-1 text-[12px] font-medium text-white"
                >
                  <Camera class="h-[13px] w-[13px]" />
                  {{ store.pictures_count ? `${store.pictures_count} photos` : 'Add photos' }}
                </span>
              </div>
            </router-link>
            <div v-if="store.pictures_count > 1" class="grid grid-cols-4 gap-1.5 p-1.5">
              <router-link
                v-for="(url, i) in store.thumbnail_urls"
                :key="i"
                :to="{ name: 'StoreGallery', params: { store_id: storeId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block overflow-hidden"
              >
                <img :src="url" class="h-full w-full object-cover" alt="" />
              </router-link>
              <router-link
                v-for="n in Math.max(0, 3 - store.thumbnail_urls.length)"
                :key="`ph-${n}`"
                :to="{ name: 'StoreGallery', params: { store_id: storeId } }"
                class="h-[54px] rounded border border-line bg-ink-50 block"
              />
              <router-link
                :to="{ name: 'StoreGallery', params: { store_id: storeId } }"
                class="flex h-[54px] items-center justify-center rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-ink-400 transition-colors hover:bg-ink-50"
              >
                <Plus class="h-4 w-4" />
              </router-link>
            </div>
          </div>

          <!-- Description -->
          <div
            v-if="store.description"
            class="bg-white border border-[#e2e4e6] rounded-sm p-4 text-[14px] text-[#3a3e44] leading-relaxed"
          >
            {{ store.description }}
          </div>

          <!-- Stats -->
          <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-[#eef0f1] font-display font-semibold text-[16px]">
              Summary
            </div>
            <div class="px-4 py-1.5">
              <div class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]">
                <span class="text-[14px] text-[#6b7077]">Orders</span>
                <span class="font-mono text-[14px]">{{ store.orders_count }}</span>
              </div>
              <div class="flex items-center justify-between py-[9px] border-b border-[#f1f2f3]">
                <span class="text-[14px] text-[#6b7077]">Total rounds</span>
                <span class="font-mono text-[14px]">{{ store.total_rounds.toLocaleString() }}</span>
              </div>
              <div class="flex items-center justify-between py-[9px]">
                <span class="text-[14px] text-[#6b7077]">Total spent</span>
                <span class="font-mono text-[14px]">{{ formatCurrency(store.total_spent) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Purchase history -->
        <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
          <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
            <span class="font-display font-semibold text-[18px]">Purchase History</span>
            <span class="font-mono text-[11px] text-muted tracking-[0.04em]">
              {{ store.orders_count }} ORDER{{ store.orders_count !== 1 ? 'S' : '' }}
            </span>
          </div>

          <div
            v-if="!store.orders?.length"
            class="px-[18px] py-12 text-center text-muted text-[14px]"
          >
            No purchases recorded yet.
          </div>

          <div v-else>
            <!-- Table header -->
            <div
              class="grid grid-cols-[1fr_auto_auto_auto] gap-4 px-[18px] py-2.5 border-b border-[#eef0f1] bg-[#fafbfb]"
            >
              <span class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098]">DATE</span>
              <span class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098] text-right"
                >REF</span
              >
              <span class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098] text-right"
                >ROUNDS</span
              >
              <span class="font-mono text-[10px] tracking-[0.06em] text-[#8a9098] text-right"
                >COST</span
              >
            </div>
            <div class="divide-y divide-[#f1f2f3]">
              <div
                v-for="order in store.orders"
                :key="order.id"
                class="grid grid-cols-[1fr_auto_auto_auto] gap-4 px-[18px] py-3 items-center hover:bg-[#fafbfb] transition-colors"
              >
                <span class="text-[14px]">{{ dayjs(order.order_date).format('MMM D, YYYY') }}</span>
                <span class="font-mono text-[13px] text-[#8a9098]">{{
                  order.order_ref ?? '—'
                }}</span>
                <span class="font-mono text-[14px] text-right">{{
                  order.rounds.toLocaleString()
                }}</span>
                <span class="font-mono text-[14px] text-right">{{
                  formatCurrency(order.total_cost)
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
