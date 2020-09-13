<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Calibers..." />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(caliber, i) in calibers" :key="i" v-else>
      <CaliberCard :caliber="caliber" />
    </div>
  </div>
</template>

<script>
import CaliberCard from './CaliberCard';
import LoadingCard from 'components/status/LoadingCard';

export default {
  name: 'CaliberList',
  components: { CaliberCard, LoadingCard },
  props: {
    calibers: {
      type: Array,
      required: true,
    },
    isLoading: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
};
</script>

<style></style>
