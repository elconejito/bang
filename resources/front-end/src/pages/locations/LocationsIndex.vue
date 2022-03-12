<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Locations</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Locations</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <button
          type="button"
          class="btn btn-outline-primary"
          data-bs-toggle="modal"
          data-bs-target="#location-form"
        >
          <font-awesome-icon icon="plus-circle" /> Add Location
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

    <LocationList :locations="locations" />

    <Modal modalId="location-form">
      <template v-slot:modalTitle>Add Location</template>
      <template v-slot:modalBody>
        Location Form
      </template>
    </Modal>
  </div>
</template>

<script>
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';
import Modal from 'components/Modal';
import LocationList from 'components/locations/LocationList';

export default {
  name: 'LocationsIndex',
  components: { LocationList, Modal },
  mixins: [HasLoading, HasModal],
  data() {
    return {
      error: false,
      locations: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeAddLocation() {
      this.closeModal('location-form');
      this.fetchLocations();
    },
    fetchData() {
      this.isLoading = true;
      this.fetchLocations();
    },
    fetchLocations() {
      this.$set(this.loadingQueue, 'locations', false);

      this.$store
        .dispatch('locations/all')
        .then((response) => {
          console.log('LocationsIndex fetchLocations() then', response);
          const { data } = response;
          this.locations = data;
        })
        .catch((error) => {
          // show the error message
          console.error('LocationsIndex fetchLocations() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.locations = true;
        });
    },
  },
};
</script>

<style></style>
