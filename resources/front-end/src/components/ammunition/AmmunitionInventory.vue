<template>
  <div class="row">
    <div class="col">
      <h3>Inventory</h3>

      <div class="row">
        <div class="col toolbar">
          <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#create-inventory-form"
          >
            Add Inventory Entry
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
        <template v-slot:modalTitle>Add Inventory Entry</template>
        <template v-slot:modalBody>
          <InventoryForm @complete="completeAddInventory" />
        </template>
      </Modal>

    </div>
  </div>
</template>

<script>
import InventoryList from 'components/inventory/InventoryList';
import HasLoading from 'mixins/HasLoading';
import Modal from 'components/Modal';
import InventoryForm from 'components/inventory/InventoryForm';
import HasModal from 'mixins/HasModal';

export default {
  name: 'AmmunitionInventory',
  components: { InventoryForm, Modal, InventoryList },
  mixins: [HasLoading, HasModal],
  data() {
    return {
      inventory: [],
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeAddInventory() {
      this.closeModal('create-inventory-form');
      this.$set(this.loadingQueue, 'inventory', false);
      this.fetchInventory();
    },
    fetchData() {
      this.isLoading = true;
      this.$set(this.loadingQueue, 'inventory', false);

      this.fetchInventory();
    },
    fetchInventory() {
      console.log('AmmunitionInventory fetchInventory()');
      const payload = {
        ammunitionId: this.ammunitionId,
      };

      return this.$store
        .dispatch('inventories/get', payload)
        .then((response) => {
          console.log('AmmunitionInventory fetchInventory() then', response);
          const { data } = response;
          this.inventory = data;
        })
        .catch((error) => {
          console.error('AmmunitionInventory fetchInventory() catch', error);
        })
        .finally(() => {
          this.loadingQueue.inventory = true;
        });
    },
  },
};
</script>

<style></style>
