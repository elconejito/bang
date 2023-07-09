<template>
  <form>
    <div class="form-group">
      <label for="rounds">Label <span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="label"
        name="label"
        required
        v-model="store.label"
      />
    </div>

    <div class="form-group">
      <label for="inventory_date">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="store.description"
      ></textarea>
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
  name: 'StoreForm',
  components: { FormError, ActionButton },
  data() {
    return {
      error: false,
      loading: false,
      store: {
        label: '',
        description: '',
      },
    };
  },
  methods: {
    formatData() {
      // Start with the current inventory object
      const data = Object.assign({}, this.store);
      // Make any tweaks as needed

      return data;
    },
    submit() {
      console.log('StoreForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.formatData(),
      };

      // submit to api
      this.$store
        .dispatch('stores/store', data)
        .then((response) => {
          console.log('StoreForm submit() dispatch then', response, data);
          this.$router.push({ name: 'StoreIndex' });
        })
        .catch((error) => {
          console.error('StoreForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        });
    },
  },
};
</script>

<style></style>
