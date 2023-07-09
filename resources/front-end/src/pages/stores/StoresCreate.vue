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
          <router-link :to="{ name: 'StoreIndex' }">All Stores</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">New Store</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add a Store</h1>
      </div>
    </div>

    <StoreForm ammunition="i" />
  </div>
</template>

<script>
import HasLoading from 'mixins/HasLoading';
import StoreForm from 'components/stores/StoreForm.vue';

export default {
  name: 'StoresCreate',
  components: { StoreForm },
  mixins: [HasLoading],
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

<style scoped></style>
