<script setup>
defineProps({
  orders: { type: Array, default: () => [] },
});

const orderId = defineModel('orderId', { type: [Number, String], default: null });
const cost = defineModel('cost', { type: [Number, String], default: null });
</script>

<template>
  <div class="grid gap-4 sm:col-span-2 sm:grid-cols-2">
    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
      <span>Purchase order <span class="font-normal text-ink-400">Optional</span></span>
      <select
        v-model="orderId"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
      >
        <option :value="null">No order selected</option>
        <option v-for="order in orders" :key="order.id" :value="order.id">
          {{ order.store?.label ? `${order.store.label} · ` : ''
          }}{{ order.order_ref || `Order #${order.id}`
          }}{{ order.order_date ? ` · ${order.order_date}` : '' }}
        </option>
      </select>
    </label>
    <label class="flex flex-col gap-1.5 text-sm font-semibold text-ink-700">
      <span>Cost per magazine <span class="font-normal text-ink-400">Optional</span></span>
      <input
        v-model.number="cost"
        type="number"
        min="0"
        step="0.01"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-2.5 font-normal outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
        placeholder="0.00"
      />
    </label>
  </div>
</template>
