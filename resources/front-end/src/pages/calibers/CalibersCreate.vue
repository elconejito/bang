<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">All Calibers</router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Create Caliber</h1>
      </div>
    </div>

    <CaliberForm @complete="completeAddCaliber" />
  </div>
</template>

<script>
import CaliberForm from '../../components/caliber/CaliberForm';
import Loading from '../../components/Loading';
import HasLoading from '../../mixins/HasLoading';

export default {
  name: 'CalibersCreate',
  components: { Loading, CaliberForm },
  mixins: [HasLoading],
  data() {
    return {
      caliber: {},
    };
  },
  methods: {
    completeAddCaliber() {
      this.$router.push({ name: 'CalibersIndex' });
    },
  },
};
</script>

<style scoped></style>
