<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Training..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(t, i) in training" :key="i" v-else>
      <TrainingCard :training="t" />
    </div>
  </div>
</template>

<script>
import LoadingCard from 'components/status/LoadingCard';
import ErrorCard from 'components/status/ErrorCard';
import EmptyCard from 'components/status/EmptyCard';
import TrainingCard from 'components/training/TrainingCard.vue';

export default {
  name: 'TrainingList',
  components: { TrainingCard, EmptyCard, ErrorCard, LoadingCard },
  props: {
    training: {
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
      return this.training.length === 0 && this.isLoading === false && this.error === false;
    },
  },
};
</script>

<style></style>
