<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Magazines</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Magazines</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <button
          type="button"
          class="btn btn-outline-primary"
          data-toggle="modal"
          data-target="#magazine-form"
        >
          <font-awesome-icon icon="plus-circle" /> Add Magazine
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

    <MagazineList :magazines="magazines" />

    <Modal modalId="magazine-form">
      <template v-slot:modalTitle>Add Magazine</template>
      <template v-slot:modalBody>
        <MagazineForm :calibers="calibers" :firearms="firearms" @complete="completeAddMagazine" />
      </template>
    </Modal>
  </div>
</template>

<script>
import MagazineForm from 'components/magazines/MagazineForm';
import Modal from 'components/Modal';
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';
import MagazineList from 'components/magazines/MagazineList';

export default {
  name: 'MagazinesIndex',
  components: { MagazineForm, MagazineList, Modal },
  mixins: [HasLoading, HasModal],
  data() {
    return {
      error: false,
      calibers: [],
      firearms: [],
      magazines: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeAddMagazine() {
      this.closeModal('magazine-form');
      this.fetchMagazines();
    },
    fetchData() {
      this.isLoading = true;
      this.fetchCalibers();
      this.fetchFirearms();
      this.fetchMagazines();
    },
    fetchCalibers() {
      this.$set(this.loadingQueue, 'calibers', false);

      this.$store
        .dispatch('calibers/all')
        .then((response) => {
          console.log('MagazinesIndex fetchCalibers() then', response);
          const { data } = response;
          this.calibers = data;
        })
        .catch((error) => {
          // show the error message
          console.error('MagazinesIndex fetchCalibers() then', error);
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
          const { data } = response;
          this.firearms = data;
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
    fetchMagazines() {
      this.$set(this.loadingQueue, 'magazines', false);

      this.$store
        .dispatch('magazines/all')
        .then((response) => {
          console.log('MagazinesIndex fetchMagazines() then', response);
          const { data, meta } = response;
          this.magazines = data;
          this.meta = meta || {};
        })
        .catch((error) => {
          // show the error message
          console.error('MagazinesIndex fetchMagazines() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.magazines = true;
        });
    },
  },
};
</script>

<style></style>
