<template>
  <div class="card status-card">
    <div class="card-body text-center">
      <font-awesome-icon icon="exclamation-triangle" class="mr-1" />
    </div>
    <div class="card-body" v-if="message">
      {{ message }}
      <ul v-if="errors">
        <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ErrorCard',
  props: {
    error: {
      type: Error,
      required: false,
    },
  },
  computed: {
    message() {
      console.log('FormError message()', this.error);
      if (this.error.response && this.error.response.data) {
        return this.error.response.data.message
          ? this.error.response.data.message
          : 'Unknown error';
      }

      return 'Unknown error';
    },
    errors() {
      console.log('FormError errors()', this.error.errorBag);
      if (this.error.errorBag) {
        return Object.keys(this.error.errorBag).map((key) => {
          return this.error.errorBag[key][0];
        });
      }
      return false;
    },
  },
};
</script>

<style scoped></style>
