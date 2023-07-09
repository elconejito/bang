<template>
  <form>
    <div class="form-group">
      <label for="rounds">Number of rounds <span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="rounds"
        name="rounds"
        placeholder="Number of Rounds"
        required
        v-model="inventory.rounds"
      />
      <small class="form-text text-muted">
        How many rounds are you adding or subtracting to your inventory
      </small>
    </div>

    <div class="form-group">
      <label for="inventory_date">Date <span class="form-required">*</span></label>
      <v-date-picker v-model="inventory.inventory_date" mode="date">
        <template v-slot="{ inputValue, inputEvents }">
          <input
            class="form-control"
            id="inventory_date"
            :value="inputValue"
            v-on="inputEvents"
          />
        </template>
      </v-date-picker>
      <small class="form-text text-muted">
        When did you add or remove this inventory?
      </small>
    </div>

    <div class="form-check">
      <input
        class="form-check-input"
        type="checkbox"
        value="1"
        id="is-purchase"
        v-model="inventory.is_purchase"
        true-value="1"
        false-value="0"
      />
      <label class="form-check-label" for="is-purchase">
        Was this a purchase?
      </label>
    </div>

    <template v-if="inventory.is_purchase === '1'">
      <div class="form-group">
        <label for="store" class="form-label">Choose the store</label>
        <select class="form-select" id="store" name="store" v-model="inventory.store_id">
          <option selected>- Select One -</option>
          <option v-for="(store, i) in stores" :value="store.id" :key="i">{{ store.label }}</option>
        </select>
      </div>

      <div class="form-group">
        <label for="cost" class="form-label">Amount</label>
        <input
          type="text"
          class="form-control"
          id="cost"
          name="cost"
          required
          v-model="inventory.cost"
        />
      </div>
    </template>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import ActionButton from '../ActionButton';
import FormError from '../FormError';
import HasLoading from 'mixins/HasLoading';

export default {
  name: 'InventoryForm',
  components: { FormError, ActionButton },
  mixins: [HasLoading],
  props: {
    ammunition: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      error: false,
      loading: false,
      stores: [],
      inventory: {
        inventory_date: new Date(),
        rounds: '',
        is_purchase: '',
        store_id: '',
        cost: 0,
        ammunition_id: this.ammunition.id,
      },
      dateAttributes: [
        {
          key: 'today',
          highlight: true,
        },
      ],
    };
  },
  methods: {
    fetchStores() {
      console.log('InventoryForm fetchStores()');
      this.$set(this.loadingQueue, 'stores', false);

      this.$store
        .dispatch('stores/all')
        .then((response) => {
          console.log('InventoryForm fetchStores() then', response);
          const { data } = response;
          this.stores = data;
        })
        .catch((error) => {
          // show the error message
          console.error('InventoryForm fetchStores() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.stores = true;
        });
    },
    formatData() {
      // Start with the current inventory object
      const data = Object.assign({}, this.inventory);
      // Make any tweaks as needed
      data.inventory_date = data.inventory_date.toISOString();

      return data;
    },
    submit() {
      console.log('InventoryForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.formatData(),
      };

      // submit to api
      this.$store
        .dispatch('inventories/store', data)
        .then((response) => {
          console.log('InventoryForm submit() dispatch then', response, data);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('InventoryForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('InventoryForm submit() dispatch finally', response, data);
          // reset statuses
          this.loading = false;
        });
    },
  },
  mounted() {
    console.log('InventoryForm mounted()');
    this.fetchStores();
  },
};
</script>

<style></style>
