<script setup>
import { computed, onMounted, ref } from 'vue';
import dayjs from 'dayjs';
import { CalendarDays, ChevronRight, Plus, RotateCcw, Search, Store } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import ErrorCard from '@/components/status/ErrorCard.vue';
import { useOrdersStore } from '@/stores/orders';
import { orderItemSearchText, orderItemLabel } from '@/composables/useOrderItems';

const ordersStore = useOrdersStore();

const orders = ref([]);
const loading = ref(true);
const error = ref(null);
const search = ref('');
const storeId = ref('');
const dateFrom = ref('');
const dateTo = ref('');

const crumbs = [{ label: 'Home', to: '/' }, { label: 'Orders' }];

const availableStores = computed(() => {
  const byId = new Map();

  for (const order of orders.value) {
    if (order.store) {
      byId.set(order.store.id, order.store);
    }
  }

  return [...byId.values()].sort((first, second) => first.label.localeCompare(second.label));
});

const filtersActive = computed(() =>
  Boolean(search.value.trim() || storeId.value || dateFrom.value || dateTo.value)
);

const filteredOrders = computed(() => {
  const query = search.value.trim().toLowerCase();

  return orders.value.filter((order) => {
    if (storeId.value && Number(order.store_id ?? order.store?.id) !== Number(storeId.value)) {
      return false;
    }

    if (dateFrom.value && order.order_date < dateFrom.value) {
      return false;
    }

    if (dateTo.value && order.order_date > dateTo.value) {
      return false;
    }

    if (!query) {
      return true;
    }

    const searchableValues = [
      order.order_ref,
      order.store?.label,
      order.order_date,
      ...(order.items ?? []).flatMap((item) => [orderItemSearchText(item)]),
    ];

    return searchableValues.some((value) =>
      String(value ?? '')
        .toLowerCase()
        .includes(query)
    );
  });
});

const countLabel = computed(() => {
  if (loading.value) {
    return undefined;
  }

  if (filtersActive.value) {
    return `${filteredOrders.value.length} OF ${orders.value.length}`;
  }

  return `${orders.value.length} ${orders.value.length === 1 ? 'ORDER' : 'ORDERS'}`;
});

function clearFilters() {
  search.value = '';
  storeId.value = '';
  dateFrom.value = '';
  dateTo.value = '';
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(
    Number(value) || 0
  );
}

function itemSummary(order) {
  const firstItem = order.items?.[0];
  const firstLabel = orderItemLabel(firstItem);
  const count = order.items_count ?? order.items?.length ?? 0;
  const remainingCount = Math.max(count - 1, 0);

  if (!firstLabel) {
    return `${count} ${count === 1 ? 'item' : 'items'}`;
  }

  return remainingCount ? `${firstLabel} +${remainingCount} more` : firstLabel;
}

onMounted(async () => {
  try {
    const ordersResponse = await ordersStore.fetchAll();
    orders.value = ordersResponse.data ?? ordersResponse;
  } catch (exception) {
    error.value = exception;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <PageHeader title="Orders" :count="countLabel" class="mb-5">
      <template #actions>
        <router-link
          :to="{ name: 'OrderCreate' }"
          class="inline-flex items-center gap-[7px] rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold text-ink-900 transition-colors hover:bg-brass-600"
        >
          <Plus class="h-4 w-4" />Add Order
        </router-link>
      </template>
    </PageHeader>

    <div class="index-toolbar mb-7 flex flex-wrap items-end gap-2.5">
      <div class="index-toolbar-search">
        <Search class="h-[17px] w-[17px] shrink-0 text-muted" />
        <input
          v-model="search"
          type="search"
          placeholder="Search store, reference, or ammo…"
          aria-label="Search orders"
          class="placeholder:text-muted"
        />
      </div>

      <label class="flex min-w-[180px] flex-col gap-1">
        <span class="font-mono text-[9px] uppercase tracking-[0.07em] text-muted">Store</span>
        <span class="relative">
          <Store
            class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
          />
          <select
            v-model="storeId"
            aria-label="Filter by store"
            class="h-10 w-full appearance-none rounded border border-[#c2c6ca] bg-white pl-9 pr-3 text-[14px] text-ink-700"
          >
            <option value="">All stores</option>
            <option
              v-for="storeOption in availableStores"
              :key="storeOption.id"
              :value="storeOption.id"
            >
              {{ storeOption.label }}
            </option>
          </select>
        </span>
      </label>

      <label class="flex flex-col gap-1">
        <span class="font-mono text-[9px] uppercase tracking-[0.07em] text-muted">From</span>
        <span class="relative">
          <CalendarDays
            class="pointer-events-none absolute left-3 top-1/2 hidden h-[15px] w-[15px] -translate-y-1/2 text-muted sm:block"
          />
          <input
            v-model="dateFrom"
            type="date"
            aria-label="Orders from date"
            class="h-10 rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[13px] text-ink-700 sm:pl-9"
          />
        </span>
      </label>

      <label class="flex flex-col gap-1">
        <span class="font-mono text-[9px] uppercase tracking-[0.07em] text-muted">To</span>
        <span class="relative">
          <CalendarDays
            class="pointer-events-none absolute left-3 top-1/2 hidden h-[15px] w-[15px] -translate-y-1/2 text-muted sm:block"
          />
          <input
            v-model="dateTo"
            type="date"
            aria-label="Orders through date"
            class="h-10 rounded border border-[#c2c6ca] bg-white px-3 font-mono text-[13px] text-ink-700 sm:pl-9"
          />
        </span>
      </label>

      <button
        v-if="filtersActive"
        type="button"
        class="inline-flex h-10 items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-3 text-[14px] font-medium text-ink-700 transition-colors hover:bg-ink-50"
        @click="clearFilters"
      >
        <RotateCcw class="h-[14px] w-[14px]" />Clear
      </button>
    </div>

    <LoadingState v-if="loading" message="Loading orders…" />

    <ErrorCard v-else-if="error" :error="error" />

    <EmptyState
      v-else-if="!orders.length"
      title="No orders yet"
      message="Add an order to connect a purchase with its store, costs, and ammunition inventory."
      action-label="Add Order"
      :action-to="{ name: 'OrderCreate' }"
    />

    <div
      v-else-if="!filteredOrders.length"
      class="rounded border border-line bg-surface px-6 py-12 text-center"
    >
      <h2 class="font-display text-[17px] font-semibold text-ink-900">No orders match</h2>
      <p class="mt-1 text-[14px] text-muted">Try adjusting your search, store, or date filters.</p>
      <button
        type="button"
        class="mt-5 inline-flex items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-4 py-2 text-[14px] font-semibold text-ink-700 hover:bg-ink-50"
        @click="clearFilters"
      >
        <RotateCcw class="h-[14px] w-[14px]" />Clear filters
      </button>
    </div>

    <div v-else class="overflow-hidden rounded border border-line bg-surface">
      <div
        class="hidden grid-cols-[140px_minmax(180px,1.25fr)_minmax(120px,0.8fr)_70px_100px_112px_20px] border-b border-line bg-ink-50 font-mono text-[10px] uppercase tracking-[0.06em] text-muted md:grid"
      >
        <div class="px-4 py-2.5">Date</div>
        <div class="px-3 py-2.5">Store & items</div>
        <div class="px-3 py-2.5">Reference</div>
        <div class="px-3 py-2.5 text-right">Items</div>
        <div class="px-3 py-2.5 text-right">Rounds</div>
        <div class="px-3 py-2.5 text-right">Total</div>
        <div />
      </div>

      <router-link
        v-for="order in filteredOrders"
        :key="order.id"
        :to="{ name: 'OrderShow', params: { order_id: order.id } }"
        data-testid="order-row"
        class="grid grid-cols-2 gap-x-4 gap-y-3 border-b border-ink-100 px-4 py-4 text-ink-900 transition-colors last:border-b-0 hover:bg-[#fafbfb] md:grid-cols-[140px_minmax(180px,1.25fr)_minmax(120px,0.8fr)_70px_100px_112px_20px] md:items-center md:gap-0 md:px-0 md:py-0"
      >
        <div class="md:px-4 md:py-3">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Date</span
          >
          <span class="text-[14px]">{{ dayjs(order.order_date).format('MMM D, YYYY') }}</span>
        </div>
        <div class="col-span-2 min-w-0 md:col-span-1 md:px-3 md:py-3">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Store & items</span
          >
          <div class="truncate font-display text-[15px] font-semibold">
            {{ order.store?.label ?? 'Unknown store' }}
          </div>
          <div class="truncate text-[12px] text-muted">{{ itemSummary(order) }}</div>
        </div>
        <div class="min-w-0 md:px-3 md:py-3">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Reference</span
          >
          <span class="block truncate font-mono text-[13px]">{{ order.order_ref ?? '—' }}</span>
        </div>
        <div class="md:px-3 md:py-3 md:text-right">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Items</span
          >
          <span class="font-mono text-[14px]">{{ order.items?.length ?? 0 }}</span>
        </div>
        <div class="md:px-3 md:py-3 md:text-right">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Rounds</span
          >
          <span class="font-mono text-[14px]">{{
            Number(order.rounds || 0).toLocaleString()
          }}</span>
        </div>
        <div class="text-right md:px-3 md:py-3">
          <span class="mb-1 block font-mono text-[9px] uppercase tracking-wide text-muted md:hidden"
            >Total</span
          >
          <span class="font-mono text-[14px] font-medium">{{
            formatCurrency(order.total_cost)
          }}</span>
        </div>
        <ChevronRight class="hidden h-4 w-4 text-ink-300 md:block" />
      </router-link>
    </div>
  </div>
</template>
