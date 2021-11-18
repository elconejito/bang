<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Firearms</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Firearms</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <button
          type="button"
          class="btn btn-outline-primary"
          data-bs-toggle="modal"
          data-bs-target="#firearm-form"
        >
          <font-awesome-icon icon="plus-circle" /> Add Firearm
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

    <FirearmList :firearms="firearms" :is-loading="isLoading" :error="error" />

    <Modal modalId="firearm-form">
      <template v-slot:modalTitle>Add Firearm</template>
      <template v-slot:modalBody>
        <FirearmForm :calibers="calibers" @complete="completeAddFirearm" />
      </template>
    </Modal>
  </div>
</template>

<script>
import Modal from '../../components/Modal';
import HasLoading from '../../mixins/HasLoading';
import FirearmList from 'components/firearms/FirearmList';
import FirearmForm from 'components/firearms/FirearmForm';
import HasModal from 'mixins/HasModal';

export default {
  name: 'FirearmsIndex',
  components: { FirearmForm, FirearmList, Modal },
  mixins: [HasLoading, HasModal],
  data() {
    return {
      error: false,
      calibers: [],
      firearms: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeAddFirearm() {
      this.closeModal('firearm-form');
      this.fetchFirearms();
    },
    fetchData() {
      this.isLoading = true;
      this.fetchCalibers();
      this.fetchFirearms();
    },
    fetchCalibers() {
      this.$set(this.loadingQueue, 'calibers', false);

      this.$store
        .dispatch('calibers/all')
        .then((response) => {
          console.log('FirearmsIndex fetchCalibers() then', response);
          const { data } = response;
          this.calibers = data;
        })
        .catch((error) => {
          // show the error message
          console.error('FirearmsIndex fetchCalibers() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.calibers = true;
        });
    },
    fetchFirearms() {
      this.$set(this.loadingQueue, 'firearms', false);

      this.$store
        .dispatch('firearms/all')
        .then((response) => {
          console.log('FirearmsIndex fetchFirearms() then', response);
          const { data, meta } = response;
          this.firearms = data;
          this.meta = meta || {};
        })
        .catch((error) => {
          // show the error message
          console.error('FirearmsIndex fetchFirearms() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.firearms = true;
        });
    },
  },
};
</script>

<style></style>
