<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Calibers..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isEmpty && !isLoading">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(caliber, i) in calibers" :key="i" v-else>
      <CaliberCard :caliber="caliber" />
    </div>
  </div>
</template>

<script>
import CaliberCard from './CaliberCard';
import LoadingCard from 'components/status/LoadingCard';
import EmptyCard from 'components/status/EmptyCard.vue';

export default {
  name: 'CaliberList',
  components: {EmptyCard, CaliberCard, LoadingCard },
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
  computed: {
    isEmpty() {
      return this.calibers.length === 0;
    },
  },
};
</script>

<style></style>
