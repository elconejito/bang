<template>
  <form>
    <div class="form-group">
      <label for="label">Label <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="label" name="label" placeholder="Label of Caliber" required v-model="caliber.label">
      <small class="form-text text-muted">
        The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
      </small>
    </div>

    <div class="form-group">
      <label for="short_label">Short Label <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="short_label" name="short_label" placeholder="Short Label for Caliber" required v-model="caliber.short_label">
      <small class="form-text text-muted">
        The label used throughout the app
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
      <ActionButton text="Save Changes" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script>
import ActionButton from '../ActionButton';
import FormError from '../FormError';
import HasForm from '../../mixins/HasForm';

export default {
  name: 'EditCaliberForm',
  components: { FormError, ActionButton },
  mixins: [HasForm],
  props: {
    original: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      error: false,
      loading: false,
      caliber: {
        id: '',
        label: '',
        short_label: '',
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
    console.log('EditCaliberForm mounted()', Object.assign({}, this.original));
    this.fetchData();
    this.initData('caliber');
  },
  methods: {
    fetchData() {
      this.$store.dispatch('reference/get', { model: 'caliberType' });
    },
    submit() {
      console.log('EditCaliberForm submit()');
      // init statuses
      this.error = false;
      this.loading = true;

      // gather data
      const payload = {
        data: this.caliber,
        id: this.caliber.id,
      };

      // submit to api
      this.$store
        .dispatch('calibers/update', payload)
        .then((response) => {
          console.log('EditCaliberForm submit() dispatch then', response, payload);
          this.$emit('complete');
        })
        .catch((error) => {
          console.error('EditCaliberForm submit() dispatch catch', error, payload);
          this.error = this.$errorProcessor(error);
        })
        .finally((response) => {
          console.log('EditCaliberForm submit() dispatch finally', response, payload);
          this.loading = false;
        });
      // reset statuses
    },
  },
};
</script>

<style></style>
