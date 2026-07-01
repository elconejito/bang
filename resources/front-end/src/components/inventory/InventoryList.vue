<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-left text-gray-600">
        <tr>
          <th class="px-4 py-3 font-medium">Date</th>
          <th class="px-4 py-3 text-right font-medium">Rounds</th>
          <th class="px-4 py-3 text-right font-medium">Purchased</th>
          <th class="px-4 py-3 text-right font-medium">Cost</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <tr v-if="isEmpty && !isLoading">
          <td colspan="4">
            <Empty message="No Inventory" />
          </td>
        </tr>
        <tr v-if="isLoading">
          <td colspan="4" class="p-4 text-center">
            <Loading />
          </td>
        </tr>
        <InventoryListRow v-for="(item, i) in inventory" :key="i" :inventory="item" />
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Empty from '@/components/Empty.vue';
import Loading from '@/components/Loading.vue';
import InventoryListRow from '@/components/inventory/InventoryListRow.vue';

const props = defineProps({
  inventory: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
});

const isEmpty = computed(() => props.inventory.length === 0);
</script>
