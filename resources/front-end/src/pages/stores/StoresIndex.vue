<template>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">All Stores</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Stores</h1>
      </div>
    </div>

    <div class="row">
      <div class="col toolbar">
        <router-link class="btn btn-outline-primary" :to="{ name: 'StoreCreate' }">
          <font-awesome-icon icon="plus-circle" /> Add Store
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

    <StoreList :stores="stores" />

  </div>
</template>

<script>
import HasLoading from 'mixins/HasLoading';
import StoreList from 'components/stores/StoreList.vue';

export default {
  name: 'StoresIndex',
  components: { StoreList },
  mixins: [HasLoading],
  data() {
    return {
      error: false,
      stores: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.isLoading = true;
      this.fetchStores();
    },
    fetchStores() {
      this.$set(this.loadingQueue, 'stores', false);

      this.$store
        .dispatch('stores/all')
        .then((response) => {
          console.log('StoresIndex fetchStores() then', response);
          const { data } = response;
          this.stores = data;
        })
        .catch((error) => {
          // show the error message
          console.error('StoresIndex fetchStores() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.stores = true;
        });
    },
  },
};
</script>

<style></style>
