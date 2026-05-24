<template>
  <div>
    <div class="mb-4 flex items-center justify-between">
      <h3 class="text-base font-semibold text-gray-900">Inventory</h3>
      <button
        type="button"
        class="inline-flex items-center gap-1.5 rounded border border-blue-600 bg-blue-600 px-3 py-1.5 text-sm text-white transition-colors hover:bg-blue-700"
      >
        Add Inventory
      </button>
    </div>

    <InventoryList :inventory="inventory" :is-loading="isLoading" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInventoriesStore } from '@/stores/inventories'
import { useLoading } from '@/composables/useLoading'
import InventoryList from '@/components/inventory/InventoryList.vue'

const props = defineProps({
  training: {
    type: Object,
    required: true,
  },
})

const inventoriesStore = useInventoriesStore()
const { isLoading, loadingQueue } = useLoading()

const inventory = ref([])

onMounted(() => fetchInventory())

async function fetchInventory() {
  isLoading.value = true
  loadingQueue.inventory = false
  try {
    const { data } = await inventoriesStore.fetchAll({
      with: 'order',
      orderBy: 'inventory_date',
      search: `training_session_id:${props.training.id}`,
    })
    inventory.value = data
  } finally {
    loadingQueue.inventory = true
  }
}
</script>
