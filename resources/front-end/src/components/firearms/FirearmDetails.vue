<template>
  <div class="row">
    <div class="col-sm-6">
      <h3>Details</h3>
      <dl class="row">
        <dt class="col-sm-4">Manufacturer</dt>
        <dd class="col-sm-8">{{ firearm.manufacturer }}</dd>

        <dt class="col-sm-4">Model</dt>
        <dd class="col-sm-8">{{ firearm.model }}</dd>

        <dt class="col-sm-4">Label</dt>
        <dd class="col-sm-8">{{ firearm.label }}</dd>
      </dl>
    </div>
    <div class="col-sm-6">
      <h3>Notes</h3>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FirearmDetails',
  props: {
    firearm: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
