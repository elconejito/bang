<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Firearms..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(firearm, i) in firearms" :key="i" v-else>
      <FirearmCard :firearm="firearm" />
    </div>
  </div>
</template>

<script>
import FirearmCard from 'components/firearms/FirearmCard';
import LoadingCard from 'components/status/LoadingCard';
import ErrorCard from 'components/status/ErrorCard';
import EmptyCard from 'components/status/EmptyCard';

export default {
  name: 'FirearmList',
  components: { EmptyCard, ErrorCard, FirearmCard, LoadingCard },
  props: {
    firearms: {
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
      return this.firearms.length === 0 && this.isLoading === false && this.error === false;
    },
  },
};
</script>

<style></style>
