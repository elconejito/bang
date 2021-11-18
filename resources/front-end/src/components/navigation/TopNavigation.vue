<template>
  <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
    <div class="container">
      <router-link to="/" class="navbar-brand">Bang</router-link>
      <button
        class="navbar-toggler navbar-toggler-right"
        type="button"
        data-toggle="collapse"
        data-target="#primary-navigation"
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
        </ul>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav navbar-right">
          <!-- Authentication Links -->
          <li class="nav-item dropdown">
            <router-link
              to="#"
              class="nav-link dropdown-toggle"
              data-bs-toggle="dropdown"
              role="button"
              aria-expanded="false"
            >
              USERNAME
            </router-link>

            <div class="dropdown-menu" role="menu">
              <router-link to="/logout">Logout</router-link>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'TopNavigation',
};
</script>

<style scoped></style>
