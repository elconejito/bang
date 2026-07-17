<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import OrderForm from '@/components/orders/OrderForm.vue';
import { useOrdersStore } from '@/stores/orders';

const props = defineProps({ orderId: { type: Number, required: true } });
const router = useRouter();
const ordersStore = useOrdersStore();
const order = ref(null);
const loading = ref(true);
const saving = ref(false);
const error = ref(null);
const loadError = ref(null);
const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Stores', to: { name: 'StoreIndex' } },
  {
    label: order.value?.store?.label ?? 'Order',
    to: { name: 'OrderShow', params: { order_id: props.orderId } },
  },
  { label: 'Edit' },
]);

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

async function submit(payload) {
  saving.value = true;
  error.value = null;
  try {
    await ordersStore.update(props.orderId, payload);
    router.push({ name: 'OrderShow', params: { order_id: props.orderId } });
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
    <div v-if="loading" class="py-12 text-center text-sm text-muted">Loading…</div>
    <div
      v-else-if="loadError"
      class="rounded border border-[#e4b9ae] bg-[#fbf2ef] p-5 text-[14px] text-[#8f3525]"
    >
      This order could not be loaded. It may no longer exist.
    </div>
    <template v-else>
      <h1 class="mb-1 font-display text-[28px] font-bold tracking-[-0.02em]">Edit order</h1>
      <p class="mb-[22px] text-[15px] text-[#6b7077]">
        Update the shared order details or any purchased item.
      </p>
      <OrderForm
        :initial-order="order"
        :loading="saving"
        :error="error"
        submit-label="Save changes"
        @submit="submit"
        @cancel="router.back()"
      />
    </template>
  </div>
</template>
