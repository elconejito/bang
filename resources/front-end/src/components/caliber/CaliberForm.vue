<template>
  <form>
    <div class="form-group">
      <label for="caliber">Caliber<span class="form-required">*</span></label>
      <input type="text" class="form-control" id="caliber" name="caliber" placeholder="Name of Caliber" required v-model="caliber.caliber">
      <small class="form-text text-muted">
        The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
      </small>
    </div>

    <div class="form-group">
      <label for="label">Label</label>
      <input type="text" class="form-control" id="label" name="label" placeholder="Label of Caliber" v-model="caliber.label">
      <small class="form-text text-muted">
        The label for the caliber which will be shown through the site, such as 9mm, 5.56, etc
      </small>
    </div>

    <div class="form-group">
      <label for="caliber_type_id">Caliber Type <span class="form-required">*</span></label>
      <select class="form-control" id="caliber_type_id" name="caliber_type_id" required v-model="caliber.caliber_type_id">
        <option v-for="(caliberType, i) in caliberTypes" :value="caliberType.id" :key="i">
          {{ caliberType.label }}
        </option>
      </select>
      <small class="form-text text-muted">
        The type of caliber such as rimfire, centerfire, or shotgun
      </small>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add New" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import ActionButton from '../ActionButton';
import FormError from '../FormError';

export default {
  name: 'CaliberForm',
  components: { FormError, ActionButton },
  data() {
    return {
      error: false,
      loading: false,
      caliber: {
        caliber: '',
        label: '',
        caliber_type_id: '',
      },
    };
  },
  computed: {
    caliberTypes() {
      return this.$store.getters['reference/get']('caliberType');
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('reference/get', { model: 'caliberType' }).then((response) => {
        console.log('CaliberForm fetchData() then', response);
        const { data, meta } = response;
        this.calibers = data;
        this.meta = meta || {};
      });
    },
    submit() {
      console.log('CaliberForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const data = {
        data: this.caliber,
      };

      // submit to api
      this.$store
        .dispatch('calibers/store', data)
        .then((response) => {
          console.log('CaliberForm submit() dispatch then', response, data);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('CaliberForm submit() dispatch catch', error, data);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('CaliberForm submit() dispatch finally', response, data);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style></style>
