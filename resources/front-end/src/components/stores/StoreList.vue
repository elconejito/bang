<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Stores..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(store, i) in stores" :key="i" v-else>
      <StoreCard :store="store" />
    </div>
  </div>
</template>

<script>
import LoadingCard from 'components/status/LoadingCard';
import ErrorCard from 'components/status/ErrorCard';
import EmptyCard from 'components/status/EmptyCard';
import StoreCard from 'components/stores/StoreCard.vue';

export default {
  name: 'StoreList',
  components: { StoreCard, EmptyCard, ErrorCard, LoadingCard },
  props: {
    stores: {
      type: Array,
      required: true,
    },
    isLoading: {
      type: Boolean,
      required: false,
      default: false,
    },
    error: {
      type: [Error, Boolean],
      required: false,
      default: false,
    },
  },
  computed: {
    hasError() {
      return this.error !== false;
    },
    showEmpty() {
      return this.stores.length === 0 && this.isLoading === false && this.error === false;
    },
  },
};
</script>

<style></style>
