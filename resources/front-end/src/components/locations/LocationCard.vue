<template>
  <div class="card firearm-card">
    <div class="card-header">
      <h3 class="card-title">
        {{ location.label }}
      </h3>
    </div>
    <div class="card-body">
      <p>Info TBD</p>
      <p>{{ location.type }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LocationCard',
  props: {
    location: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
