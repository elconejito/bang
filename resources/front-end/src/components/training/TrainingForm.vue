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
        v-model="training.label"
      />
    </div>

    <div class="form-group">
      <label for="inventory_date">Date <span class="form-required">*</span></label>
      <v-date-picker v-model="training.session_date" mode="date">
        <template v-slot="{ inputValue, inputEvents }">
          <input class="form-control" id="inventory_date" :value="inputValue" v-on="inputEvents" />
        </template>
      </v-date-picker>
      <small class="form-text text-muted"> When did you add or remove this inventory? </small>
    </div>

    <div class="form-group">
      <label for="inventory_date">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="training.description"
      ></textarea>
    </div>

    <div class="form-group">
      <label for="store" class="form-label">Choose the location</label>
      <select class="form-select" id="store" name="store" v-model="training.location_id">
        <option selected>- Select One -</option>
        <option v-for="(location, i) in locations" :value="location.id" :key="i">
          {{ location.label }}
        </option>
      </select>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add New" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import FormError from 'components/FormError';
import ActionButton from 'components/ActionButton';

export default {
  name: 'TrainingForm',
  components: { ActionButton, FormError },
  data() {
    return {
      error: false,
      loading: false,
      training: {
        label: '',
        description: '',
        session_date: '',
        location_id: '',
      },
      locations: [],
    };
  },
  methods: {
    fetchLocations() {
      this.$store
        .dispatch('locations/all')
        .then((data) => {
          console.log('TrainingForm fetchLocations() then', data);
          this.locations = data.data;
        })
        .catch((error) => {
          // show the error message
          console.error('TrainingForm fetchLocations() catch', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {});
    },
    submit() {
      console.log('TrainingForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.training,
      };

      // submit to api
      this.$store
        .dispatch('training/store', data)
        .then((response) => {
          console.log('TrainingForm submit() dispatch then', response, data);
          this.$router.push({ name: 'TrainingShow', params: { training_id: data.id } });
        })
        .catch((error) => {
          console.error('TrainingForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('TrainingForm submit() dispatch finally', response, data);
          this.loading = false;
        });
      // reset statuses
    },
  },
  mounted() {
    this.fetchLocations();
  },
};
</script>

<style></style>
