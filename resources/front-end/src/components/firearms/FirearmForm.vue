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
          v-model="firearm.manufacturer"
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
          v-model="firearm.model"
        />
        <small class="form-text text-muted">
          The Model or Version of the firearm
        </small>
      </div>
    </div>

    <div class="form-group">
      <label for="label">Label <span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="label"
        name="label"
        placeholder="Label of Firearm"
        required
        v-model="firearm.label"
      />
      <small class="form-text text-muted">
        The label that will show throughout the app
      </small>
    </div>

    <div class="form-group row">
      <div class="col-12">
        <label>Calibers</label>
        <small class="form-text text-muted">
          Check all the calibers this firearm supports
        </small>
      </div>
      <div class="col-sm-6" v-for="(caliber, i) in calibers" :key="i">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" :value="caliber.id" :id="`check-${i}`" v-model="firearm.calibers">
          <label class="form-check-label" :for="`check-${i}`">
            {{ caliber.label }}
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
  name: 'FirearmForm',
  components: { ActionButton, FormError },
  props: {
    calibers: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      error: false,
      loading: false,
      firearm: {
        label: '',
        manufacturer: '',
        model: '',
        calibers: [],
      },
    };
  },
  methods: {
    submit() {
      console.log('FirearmForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.firearm,
      };

      // submit to api
      this.$store
        .dispatch('firearms/store', data)
        .then((response) => {
          console.log('FirearmForm submit() dispatch then', response, data);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('FirearmForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('FirearmForm submit() dispatch finally', response, data);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style></style>
