<template>
  <div v-if="isLoading">
    Loading the application
  </div>
  <div v-else>
    <TopNavigation />

    <router-view />

    <SiteFooter />
  </div>
</template>

<script>
import TopNavigation from '../components/navigation/TopNavigation';
import SiteFooter from '../components/SiteFooter';
import HasLoading from 'mixins/HasLoading';

export default {
  name: 'Authenticated',
  components: { SiteFooter, TopNavigation },
  mixins: [HasLoading],
  created() {
    this.initializeApp();
  },
  methods: {
    initializeApp() {
      console.log('Authenticated initializeApp()');
      // Load references
      this.isLoading = true;
      this.loadReferences();
    },
    loadReferences() {
      console.log('Authenticated loadReferences()');

      this.$set(this.loadingQueue, 'references', false);

      this.$store
        .dispatch('reference/getAll')
        .then((response) => {
          console.log('Authenticated loadReferences() then', response);
        })
        .catch((error) => {
          console.error('Authenticated loadReferences() catch', error);
        })
        .finally(() => {
          this.loadingQueue.references = true;
        });
    },
  },
};
</script>

<style></style>
