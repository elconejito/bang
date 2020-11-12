<template>
  <form>
    <div class="form-group row">
      <div class="col-sm-4">
        <label for="manufacturer">Manufacturer <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="manufacturer"
          name="manufacturer"
          placeholder="Manufacturer"
          required
          v-model="magazine.manufacturer"
        />
        <small class="form-text text-muted">
          The name of the manufacturer
        </small>
      </div>
      <div class="col-sm-8">
        <label for="model">Model <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="model"
          name="model"
          placeholder="Model"
          required
          v-model="magazine.model_name"
        />
        <small class="form-text text-muted">
          The Model or Version of the magazine
        </small>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-sm-6">
        <label for="label">Label <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="label"
          name="label"
          placeholder="Label of magazine"
          required
          v-model="magazine.label"
        />
        <small class="form-text text-muted">
          The label that will show throughout the app
        </small>
      </div>
      <div class="col-sm-6">
        <label for="serial_number">Serial Number</label>
        <input
          type="text"
          class="form-control"
          id="serial_number"
          name="serial_number"
          placeholder="Serial of magazine"
          required
          v-model="magazine.serial_number"
        />
        <small class="form-text text-muted">
          Any serial numbers that identify the magazine
        </small>
      </div>
    </div>

    <div class="form-group">
      <label for="capacity">Capacity <span class="form-required">*</span></label>
      <input
        type="number"
        class="form-control"
        id="capacity"
        name="capacity"
        placeholder="Magazine Capacity"
        required
        v-model="magazine.capacity"
      />
      <small class="form-text text-muted">
        The max number of rounds this magazine will hold
      </small>
    </div>

    <div class="form-group row">
      <div class="col-12">
        <label>Calibers</label>
        <small class="form-text text-muted">
          Check all the calibers this magazine supports
        </small>
      </div>
      <div class="col-sm-6" v-for="(caliber, i) in calibers" :key="i">
        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            :value="caliber.id"
            :id="`check-calibers-${i}`"
            v-model="magazine.calibers"
          />
          <label class="form-check-label" :for="`check-calibers-${i}`">
            {{ caliber.label }}
          </label>
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-12">
        <label>Used By</label>
        <small class="form-text text-muted">
          Check all the firearms that can use this magazine
        </small>
      </div>
      <div class="col-sm-6" v-for="(firearm, i) in firearms" :key="i">
        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            :value="firearm.id"
            :id="`check-firearms-${i}`"
            v-model="magazine.firearms"
          />
          <label class="form-check-label" :for="`check-firearms-${i}`">
            {{ firearm.label }}
          </label>
        </div>
      </div>
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
  name: 'MagazineForm',
  components: { ActionButton, FormError },
  props: {
    calibers: {
      type: Array,
      required: true,
    },
    firearms: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      error: false,
      loading: false,
      magazine: {
        label: '',
        manufacturer: '',
        model_name: '',
        serial_number: '',
        capacity: 0,
        calibers: [],
        firearms: [],
      },
    };
  },
  methods: {
    submit() {
      console.log('MagazineForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.magazine,
      };

      // submit to api
      this.$store
        .dispatch('magazines/store', data)
        .then((response) => {
          console.log('MagazineForm submit() dispatch then', response, data);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('MagazineForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('MagazineForm submit() dispatch finally', response, data);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style></style>
