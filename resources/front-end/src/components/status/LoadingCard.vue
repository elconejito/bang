<template>
  <div class="card status-card">
    <div class="card-body text-center">
      <Loading class="h3" />
    </div>
    <div class="card-body text-center" v-if="message">
      {{ message }}
    </div>
  </div>
</template>

<script>
import Loading from 'components/Loading';

export default {
  name: 'LoadingCard',
  components: { Loading },
  props: {
    message: {
      type: String,
      required: false,
      default: '',
    },
  },
};
</script>

<style scoped></style>
