<template>
  <div class="card firearm-card">
    <div class="card-header">
      <h3 class="card-title">
        {{ store.label }}
      </h3>
    </div>
    <div class="card-body">
      <p>Info TBD</p>
      <p>{{ store.type }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StoreCard',
  props: {
    store: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
