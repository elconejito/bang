<script setup>
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import OrderForm from '@/components/orders/OrderForm.vue';
import { useOrdersStore } from '@/stores/orders';

const route = useRoute();
const router = useRouter();
const ordersStore = useOrdersStore();
const saving = ref(false);
const error = ref(null);
const initialStoreId = computed(() => {
  const id = Number(route.query.store_id);
  return Number.isInteger(id) && id > 0 ? id : null;
});
const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Stores', to: { name: 'StoreIndex' } },
  { label: 'Add order' },
];

async function submit(payload) {
  saving.value = true;
  error.value = null;
  try {
    const response = await ordersStore.create(payload);
    const order = response.data ?? response;
    router.push({ name: 'OrderShow', params: { order_id: order.id } });
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="mx-auto max-w-[860px] px-5 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <h1 class="mb-1 font-display text-[28px] font-bold tracking-[-0.02em]">Add order</h1>
    <p class="mb-[22px] text-[15px] text-[#6b7077]">
      Record everything purchased together. Each ammunition load becomes its own line.
    </p>
    <OrderForm
      :initial-store-id="initialStoreId"
      :loading="saving"
      :error="error"
      @submit="submit"
      @cancel="router.back()"
    />
  </div>
</template>
