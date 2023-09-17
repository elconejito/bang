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

<script>
import InventoryList from 'components/inventory/InventoryList';
import HasLoading from 'mixins/HasLoading';

export default {
  name: 'TrainingInventory',
  components: { InventoryList },
  mixins: [HasLoading],
  props: {
    training: {
      type: Object,
      required: true,
    },
  },
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
      console.log('TrainingInventory fetchInventory()');
      const payload = {
        params: {
          with: 'order',
          orderBy: 'inventory_date',
          search: `training_session_id:${this.training.id}`,
        },
      };

      return this.$store
        .dispatch('inventories/get', payload)
        .then((response) => {
          console.log('TrainingInventory fetchInventory() then', response);
          const { data } = response;
          this.inventory = data;
        })
        .catch((error) => {
          console.error('TrainingInventory fetchInventory() catch', error);
        })
        .finally(() => {
          this.loadingQueue.inventory = true;
        });
    },
  },
};
</script>

<style></style>
