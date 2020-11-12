<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Magazines..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(magazine, i) in magazines" :key="i" v-else>
      <MagazineCard :magazine="magazine" />
    </div>
  </div>
</template>

<script>
import LoadingCard from 'components/status/LoadingCard';
import ErrorCard from 'components/status/ErrorCard';
import EmptyCard from 'components/status/EmptyCard';
import MagazineCard from 'components/magazines/MagazineCard';

export default {
  name: 'MagazineList',
  components: { MagazineCard, EmptyCard, ErrorCard, LoadingCard },
  props: {
    magazines: {
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
      return this.magazines.length === 0 && this.isLoading === false && this.error === false;
    },
  },
};
</script>

<style></style>
