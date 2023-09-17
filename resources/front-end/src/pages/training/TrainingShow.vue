<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'TrainingIndex' }">All Training</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ training.label }}</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>Training</small><br />
          {{ training.label }}
          <button type="button" class="btn btn-outline-info">
            <font-awesome-icon icon="edit" />
          </button>
        </h1>
      </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" v-for="(tab, i) in tabNames" :key="i">
        <span
          class="btn btn-link nav-link"
          :class="{ active: tabNameSlug(tab) === currentTab }"
          @click="setCurrentTab(tabNameSlug(tab))"
        >
          {{ tab }}
        </span>
      </li>
    </ul>

    <div class="tab-content py-3">
      <component :is="currentTabComponent" :training="training" />
    </div>
  </div>
</template>

<script>
import HasNavTabs from 'mixins/HasNavTabs';
import TrainingDetails from 'components/training/TrainingDetails';
import TrainingInventory from 'components/training/TrainingInventory';

export default {
  name: 'TrainingShow',
  components: { TrainingDetails, TrainingInventory },
  mixins: [HasNavTabs],
  props: {
    trainingId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      training: {},
    };
  },
  mounted() {
    const tabMapping = {
      details: {
        active: true,
        label: 'Details',
        component: 'TrainingDetails',
      },
      inventory: {
        active: false,
        label: 'Inventory',
        component: 'TrainingInventory',
      },
      // images: {
      //   active: false,
      //   label: 'Images',
      //   component: 'TrainingImages',
      // },
    };
    this.initTabs(tabMapping);
    this.fetchTraining();
  },
  methods: {
    fetchTraining() {
      this.isLoading = true;
      this.$store
        .dispatch('training/get', { trainingId: this.trainingId })
        .then((data) => {
          console.log('TrainingShow fetchTraining() then', data);
          this.training = data;
        })
        .catch((error) => {
          // show the error message
          console.error('TrainingShow fetchTraining() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style></style>
