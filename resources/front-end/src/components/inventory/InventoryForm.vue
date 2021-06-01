<template>
  <form>
    <div class="form-group">
      <label for="rounds">Number of rounds <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="rounds" name="rounds" placeholder="Number of Rounds" required v-model="inventory.rounds">
      <small class="form-text text-muted">
        How many rounds are you adding or subtracting to your inventory
      </small>
    </div>

    <div class="form-group">
      <label for="cost">Cost <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="cost" name="cost" placeholder="Cost of the ammunition" required v-model="inventory.cost">
      <small class="form-text text-muted">
        How much did it cost?
      </small>
    </div>

    <div class="form-group">
      <label for="inventory_date">Date <span class="form-required">*</span></label>
      <v-date-picker
        v-model="inventory.inventory_date"
      >
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

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import ActionButton from '../ActionButton';
import FormError from '../FormError';

export default {
  name: 'InventoryForm',
  components: { FormError, ActionButton },
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
      inventory: {
        inventory_date: new Date(),
        rounds: '',
        cost: '',
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
    formatData() {
      this.inventory.inventory_date = this.inventory.inventory_date.toISOString();

      return this.inventory;
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
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style></style>
