<template>
  <div class="row">
    <div class="col">
      <h3>Inventory</h3>

      <div class="row">
        <div class="col toolbar">
          <button type="button" class="btn btn-primary">Add Inventory</button>
          <div class="btn-group" role="group" aria-label="View Options">
            <button type="button" class="btn btn-outline-dark">
              <font-awesome-icon icon="sort" />
            </button>
            <button type="button" class="btn btn-outline-dark">
              <font-awesome-icon icon="sliders-h" />
            </button>
          </div>
        </div>
      </div>

      <InventoryList :inventory="inventory" :is-loading="isLoading" />
    </div>
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
