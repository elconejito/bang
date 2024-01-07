<template>
  <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
    <div class="container">
      <router-link to="/" class="navbar-brand">Bang</router-link>
      <button
        class="navbar-toggler navbar-toggler-right"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#primary-navigation"
        aria-controls="primary-navigation"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <i class="fa fa-bars"></i>
      </button>

      <div class="collapse navbar-collapse justify-content-between" id="primary-navigation">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link to="/calibers" class="nav-link">Calibers</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/firearms" class="nav-link">Firearms</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/magazines" class="nav-link">Magazines</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/training" class="nav-link">Training</router-link>
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'StoreIndex' }" class="nav-link">Stores</router-link>
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'LocationIndex' }" class="nav-link">Locations</router-link>
          </li>
        </ul>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav navbar-right" v-if="isAuthenticated">
          <li class="navbar-text">{{ userName }}</li>
          <!-- Authentication Links -->
          <li class="nav-item dropdown">
            <button
              class="nav-link btn btn-outline-light"
              data-bs-toggle="dropdown"
              role="button"
              aria-expanded="false"
            >
              <font-awesome-icon icon="bars" />
            </button>

            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" role="menu">
              <li>
                <button class="dropdown-item btn btn-link" @click="callLogout">Logout</button>
              </li>
            </ul>
          </li>
        </ul>
        <ul class="navbar-nav navbar-right" v-else>
          <li class="nav-item">
            <router-link :to="{ name: 'login' }" class="nav-link">Login</router-link>
          </li>
          <li class="nav-item">
            <router-link :to="{ name: 'register' }" class="nav-link">Register</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { currentUser } from '@/store/auth/getters';

export default {
  name: 'TopNavigation',
  computed: {
    isAuthenticated() {
      return this.$store.getters['auth/isAuthenticated'];
    },
    userName() {
      return this.$store.getters['auth/currentUser'].name;
    },
  },
  methods: {
    callLogout() {
      console.log('TopNavigation callLogout()');
      this.$store
        .dispatch('auth/logout', {})
        .then((response) => {
          console.log('TopNavigation logout() then', response);
        })
        .catch((error) => {
          console.error('TopNavigation logout() catch', error);
        });
    },
  },
};
</script>

<style scoped></style>
