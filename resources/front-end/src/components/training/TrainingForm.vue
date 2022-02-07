<template>
  <form>
    <div class="form-group">
      <label for="capacity">Capacity <span class="form-required">*</span></label>
      <input
        type="number"
        class="form-control"
        id="capacity"
        name="capacity"
        placeholder="Magazine Capacity"
        required
        v-model="training.capacity"
      />
      <small class="form-text text-muted">
        The max number of rounds this magazine will hold
      </small>
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
      training: {},
    };
  },
  methods: {
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
        .dispatch('magazines/store', data)
        .then((response) => {
          console.log('TrainingForm submit() dispatch then', response, data);
          this.$emit('complete');
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
};
</script>

<style></style>
