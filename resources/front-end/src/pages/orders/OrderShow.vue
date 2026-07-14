<script setup>
import { computed, onMounted, ref } from 'vue';
import dayjs from 'dayjs';
import { Calendar, ChevronRight, Hash, Package, Pencil, Store } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useOrdersStore } from '@/stores/orders';

const props = defineProps({ orderId: { type: Number, required: true } });
const ordersStore = useOrdersStore();
const order = ref(null);
const loading = ref(true);
const loadError = ref(null);
const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Stores', to: { name: 'StoreIndex' } },
  ...(order.value?.store
    ? [
        {
          label: order.value.store.label,
          to: { name: 'StoreShow', params: { store_id: order.value.store.id } },
        },
      ]
    : []),
  { label: 'Order' },
]);

function money(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(
    Number(value) || 0
  );
}

function ammoLabel(item) {
  const ammo = item.ammunition;
  return [ammo?.manufacturer, ammo?.label].filter(Boolean).join(' · ') || 'Ammunition';
}

onMounted(async () => {
  try {
    const response = await ordersStore.fetchOne(props.orderId);
    order.value = response.data ?? response;
  } catch (err) {
    loadError.value = err;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="mx-auto max-w-[960px] px-5 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <div v-if="loading" class="py-12 text-center text-sm text-muted">Loading…</div>
    <div
      v-else-if="loadError"
      class="rounded border border-[#e4b9ae] bg-[#fbf2ef] p-5 text-[14px] text-[#8f3525]"
    >
      This order could not be loaded. It may no longer exist.
    </div>
    <template v-else-if="order">
      <div class="mb-[22px] flex flex-wrap items-start gap-4">
        <div class="min-w-0 flex-1">
          <h1 class="font-display text-[28px] font-bold tracking-[-0.02em]">
            Order from {{ order.store?.label ?? 'Unknown store' }}
          </h1>
          <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-[14px] text-[#6b7077]">
            <span class="inline-flex items-center gap-1.5"
              ><Calendar class="h-[15px] w-[15px] text-muted" />{{
                dayjs(order.order_date).format('MMM D, YYYY')
              }}</span
            >
            <span v-if="order.order_ref" class="inline-flex items-center gap-1.5"
              ><Hash class="h-[15px] w-[15px] text-muted" />{{ order.order_ref }}</span
            >
          </div>
        </div>
        <router-link
          :to="{ name: 'OrderEdit', params: { order_id: order.id } }"
          class="detail-action"
          ><Pencil class="h-[15px] w-[15px]" />Edit order</router-link
        >
      </div>

      <div
        class="mb-4 grid grid-cols-1 overflow-hidden rounded border border-line bg-white sm:grid-cols-3"
      >
        <div class="border-b border-[#eef0f1] px-5 py-4 sm:border-b-0 sm:border-r">
          <div class="font-mono text-[24px] font-medium">
            {{ Number(order.rounds || 0).toLocaleString() }}
          </div>
          <div class="font-mono text-[10px] tracking-[0.06em] text-muted">ROUNDS</div>
        </div>
        <div class="border-b border-[#eef0f1] px-5 py-4 sm:border-b-0 sm:border-r">
          <div class="font-mono text-[24px] font-medium">{{ money(order.total_cost) }}</div>
          <div class="font-mono text-[10px] tracking-[0.06em] text-muted">TOTAL</div>
        </div>
        <div class="px-5 py-4">
          <div class="font-mono text-[24px] font-medium">{{ order.items?.length ?? 0 }}</div>
          <div class="font-mono text-[10px] tracking-[0.06em] text-muted">ITEMS</div>
        </div>
      </div>

      <div class="overflow-hidden rounded border border-line bg-white">
        <div class="flex items-center gap-2 border-b border-[#eef0f1] bg-[#fafbfb] px-5 py-3.5">
          <Package class="h-[17px] w-[17px] text-[#7d6320]" /><span
            class="font-display text-[17px] font-semibold"
            >Purchased items</span
          >
        </div>
        <div
          v-for="item in order.items"
          :key="item.id"
          class="grid grid-cols-[1fr_auto] gap-4 border-b border-[#eef0f1] px-5 py-4 last:border-b-0 sm:grid-cols-[1fr_120px_120px]"
        >
          <router-link
            :to="{ name: 'AmmoShow', params: { ammunition_id: item.ammunition_id } }"
            class="group flex min-w-0 items-center gap-2 rounded text-ink-900 hover:text-[#7d6320]"
          >
            <div class="min-w-0">
              <div class="font-display text-[16px] font-semibold">{{ ammoLabel(item) }}</div>
              <div v-if="item.ammunition?.caliber" class="mt-0.5 text-[13px] text-muted">
                {{ item.ammunition.caliber.label ?? item.ammunition.caliber }}
              </div>
            </div>
            <ChevronRight
              class="h-4 w-4 shrink-0 text-[#b6bcc1] transition-colors group-hover:text-[#7d6320]"
            />
          </router-link>
          <div class="text-right">
            <div class="font-mono text-[16px]">{{ Number(item.rounds).toLocaleString() }}</div>
            <div class="font-mono text-[9px] tracking-[0.05em] text-muted">ROUNDS</div>
          </div>
          <div class="col-span-2 text-right sm:col-span-1">
            <div class="font-mono text-[16px]">{{ money(item.cost) }}</div>
            <div class="font-mono text-[9px] tracking-[0.05em] text-muted">
              {{ money(item.cost_per_round) }} / RD
            </div>
          </div>
        </div>
      </div>

      <router-link
        v-if="order.store"
        :to="{ name: 'StoreShow', params: { store_id: order.store.id } }"
        class="mt-4 inline-flex items-center gap-1.5 text-[14px] font-semibold text-brass-800 hover:text-brass-600"
        ><Store class="h-4 w-4" />Back to {{ order.store.label }}</router-link
      >
      <NotesPanel class="mt-6" entity-type="orders" :entity-id="orderId" />
    </template>
  </div>
</template>
