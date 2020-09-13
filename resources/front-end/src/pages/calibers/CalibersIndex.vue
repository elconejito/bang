<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Calibers</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Calibers</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <button
          type="button"
          class="btn btn-outline-primary"
          data-toggle="modal"
          data-target="#caliber-form"
        >
          <font-awesome-icon icon="plus-circle" /> Add Caliber
        </button>
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

    <CaliberList :calibers="calibers" :isLoading="isLoading" />

    <Modal modalId="caliber-form">
      <template v-slot:modalTitle>Add Caliber</template>
      <template v-slot:modalBody>
        <CaliberForm @complete="fetchData" />
      </template>
    </Modal>
  </div>
</template>

<script>
import CaliberList from '../../components/caliber/CaliberList';
import Modal from '../../components/Modal';
import CaliberForm from '../../components/caliber/CaliberForm';
import HasLoading from '../../mixins/HasLoading';

export default {
  name: 'CalibersIndex',
  components: { CaliberForm, Modal, CaliberList },
  mixins: [HasLoading],
  data() {
    return {
      calibers: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.isLoading = true;
      this.$set(this.loadingQueue, 'calibers', false);

      this.$store
        .dispatch('calibers/all')
        .then((response) => {
          console.log('CalibersIndex fetchData() then', response);
          const { data, meta } = response;
          this.calibers = data;
          this.meta = meta || {};
        })
        .catch((error) => {
          // show the error message
        })
        .finally(() => {
          this.loadingQueue.calibers = true;
        });
    },
  },
};
</script>

<style></style>
