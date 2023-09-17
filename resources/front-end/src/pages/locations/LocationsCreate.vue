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
          <router-link :to="{ name: 'LocationIndex' }"> All Locations </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Add Locations</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>Add Location</h1>
      </div>
    </div>

    <div class="row">
      <LocationForm />
    </div>
  </div>
</template>

<script>
import LocationForm from 'components/locations/LocationForm.vue';

export default {
  name: 'LocationsCreate',
  components: { LocationForm },
};
</script>

<style></style>
