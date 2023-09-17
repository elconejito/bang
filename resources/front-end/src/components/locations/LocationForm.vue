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
        v-model="location.label"
      />
    </div>

    <div class="form-group">
      <label for="inventory_date">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="location.description"
      ></textarea>
    </div>

    <div class="form-group">
      <label for="location_type_id">Location Type</label>
      <select class="form-control" id="location_type_id" name="location_type_id" required v-model="location.location_type_id">
        <option v-for="(locationType, i) in locationTypes" :value="locationType.id" :key="i">
          {{ locationType.label }}
        </option>
      </select>
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
  name: 'LocationForm',
  components: { FormError, ActionButton },
  data() {
    return {
      error: false,
      loading: false,
      location: {
        label: '',
        description: '',
        location_type_id: '',
      },
    };
  },
  computed: {
    locationTypes() {
      return this.$store.getters['reference/locationType'];
    },
  },
  methods: {
    formatData() {
      // Start with the current inventory object
      const data = Object.assign({}, this.location);
      // Make any tweaks as needed

      return data;
    },
    submit() {
      console.log('LocationForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.formatData(),
      };

      // submit to api
      this.$store
        .dispatch('locations/store', data)
        .then((response) => {
          console.log('LocationForm submit() dispatch then', response, data);
          this.$router.push({ name: 'LocationIndex' });
        })
        .catch((error) => {
          console.error('LocationForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('LocationForm submit() dispatch finally', response, data);
          // reset statuses
          this.loading = false;
        });
    },
  },
};
</script>

<style></style>
