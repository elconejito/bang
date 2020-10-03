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
          <input
            class="form-check-input"
            type="checkbox"
            :value="caliber.id"
            :id="`check-${i}`"
            v-model="firearm.calibers"
          />
          <label class="form-check-label" :for="`check-${i}`">
            {{ caliber.label }}
          </label>
        </div>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Save Changes" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import FormError from 'components/FormError';
import ActionButton from 'components/ActionButton';
import HasForm from 'mixins/HasForm';

export default {
  name: 'EditFirearmForm',
  components: { ActionButton, FormError },
  mixins: [HasForm],
  props: {
    original: {
      type: Object,
      required: true,
    },
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
        id: '',
        label: '',
        manufacturer: '',
        model: '',
        calibers: [],
      },
    };
  },
  mounted() {
    this.initData('firearm');
    this.formatFormData();
  },
  methods: {
    formatFormData() {
      if (this.firearm.calibers) {
        this.firearm.calibers = this.firearm.calibers.map((c) => c.id);
      }
    },
    submit() {
      console.log('EditFirearmForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const payload = {
        data: this.firearm,
        id: this.firearm.id,
      };

      // submit to api
      this.$store
        .dispatch('firearms/update', payload)
        .then((response) => {
          console.log('EditFirearmForm submit() dispatch then', response, payload);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('EditFirearmForm submit() dispatch catch', error, payload);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('EditFirearmForm submit() dispatch finally', response, payload);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style scoped></style>
