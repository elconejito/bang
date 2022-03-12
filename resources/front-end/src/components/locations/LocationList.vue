<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Locations..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(location, i) in locations" :key="i" v-else>
      <LocationCard :location="location" />
    </div>
  </div>
</template>

<script>
import LoadingCard from 'components/status/LoadingCard';
import ErrorCard from 'components/status/ErrorCard';
import EmptyCard from 'components/status/EmptyCard';
import LocationCard from 'components/locations/LocationCard';

export default {
  name: 'LocationList',
  components: { LocationCard, EmptyCard, ErrorCard, LoadingCard },
  props: {
    locations: {
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
      return this.locations.length === 0 && this.isLoading === false && this.error === false;
    },
  },
};
</script>

<style></style>
