<template>
  <div class="row">
    <div class="col">
      <h1>CALLBACK</h1>
      <!-- Check that the SDK client is not currently loading before accessing is methods -->
      <div v-if="!$auth.loading">
        <!-- show login when not authenticated -->
        <p v-if="!$auth.isAuthenticated">Not Authenticated</p>
        <!-- show logout when authenticated -->
        <p v-if="$auth.isAuthenticated">Authenticated</p>
      </div>
      <p v-else>
        STILL LOADING...
      </p>
    </div>
  </div>
</template>

<script>
import { getInstance } from '../../plugins/auth/auth0';
export default {
  name: 'Callback',
  created() {
    this.init();
  },
  methods: {
    init() {
      console.log('Callback init()');
      // Make sure auth0Client is ready
      const instance = getInstance();
      instance.$watch('loading', (loading) => {
        if (loading === false && instance.isAuthenticated) {
          this.loadTokenIntoStore(instance);
        }
      });
    },
    async loadTokenIntoStore(instance) {
      console.log('Callback loadTokenIntoStore()');
      await instance.getTokenSilently().then((authToken) => {
        // Load authToken in state store for other components to use
        this.$store.dispatch('auth/saveAuthToken', { token: authToken }).then(() => {
          this.$router.push({ name: 'dashboard' });
        });
      });
    },
  },
};
</script>

<style></style>
