<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <h1>Login</h1>
        <!-- Check that the SDK client is not currently loading before accessing is methods -->
        <div v-if="!$auth.loading">
          <!-- show login when not authenticated -->
          <button class="btn btn-primary" v-if="!$auth.isAuthenticated" @click="login">
            Log in
          </button>
          <!-- show logout when authenticated -->
          <button class="btn btn-primary" v-if="$auth.isAuthenticated" @click="logout">
            Log out
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  methods: {
    // Log the user in
    login() {
      this.$auth.loginWithRedirect();
    },
    // Log the user out
    logout() {
      this.$auth.logout({
        returnTo: window.location.origin,
      });
    },
  },
};
</script>

<style></style>
