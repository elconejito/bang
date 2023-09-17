<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Training</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Training</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link class="btn btn-outline-primary" :to="{ name: 'TrainingCreate' }">
          <font-awesome-icon icon="plus-circle" /> Add Training
        </router-link>
        <div class="btn-group" role="group" aria-label="View Options">
          <button type="button" class="btn btn-outline-dark">
            <font-awesome-icon icon="sort" />
          </button>
          <button type="button" class="btn btn-outline-dark">
            <font-awesome-icon icon="sliders-h" />
          </button>
        </div>
      </div>
    </div>

    <TrainingList :training="training" />
  </div>
</template>

<script>
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';
import TrainingList from 'components/training/TrainingList';

export default {
  name: 'TrainingIndex',
  components: { TrainingList },
  mixins: [HasLoading, HasModal],
  data() {
    return {
      error: false,
      training: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeAddTraining() {
      this.closeModal('training-form');
      this.fetchTraining();
    },
    fetchData() {
      this.isLoading = true;
      this.fetchTraining();
    },
    fetchTraining() {
      this.$set(this.loadingQueue, 'training', false);

      this.$store
        .dispatch('training/all')
        .then((response) => {
          console.log('TrainingIndex fetchTraining() then', response);
          const { data } = response;
          this.training = data;
        })
        .catch((error) => {
          // show the error message
          console.error('TrainingIndex fetchTraining() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.training = true;
        });
    },
  },
};
</script>

<style></style>
