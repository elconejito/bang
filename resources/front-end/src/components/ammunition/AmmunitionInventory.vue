<template>
  <div class="row">
    <div class="col">
      <h3>Inventory</h3>

      <div class="row">
        <div class="col toolbar">
          <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#create-inventory-form"
          >
            Add Inventory
          </button>
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

      <Modal modalId="create-inventory-form">
        <template #modalTitle>Add Inventory Entry</template>
        <template #modalBody>
          <InventoryForm :ammunition="ammunition" @complete="completeAddInventory" />
        </template>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInventoriesStore } from '@/stores/inventories'
import { useLoading } from '@/composables/useLoading'
import { useModal } from '@/composables/useModal'
import InventoryList from '@/components/inventory/InventoryList.vue'
import InventoryForm from '@/components/inventory/InventoryForm.vue'
import Modal from '@/components/Modal.vue'

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
})

const inventoriesStore = useInventoriesStore()
const { isLoading, loadingQueue } = useLoading()
const { closeModal } = useModal()

const inventory = ref([])

onMounted(() => fetchInventory())

async function fetchInventory() {
  isLoading.value = true
  loadingQueue.inventory = false
  try {
    const { data } = await inventoriesStore.fetchAll({
      with: 'order',
      orderBy: 'inventory_date',
      search: `ammunition_id:${props.ammunition.id}`,
    })
    inventory.value = data
  } finally {
    loadingQueue.inventory = true
  }
}

async function completeAddInventory() {
  closeModal('create-inventory-form')
  loadingQueue.inventory = false
  await fetchInventory()
}
</script>
