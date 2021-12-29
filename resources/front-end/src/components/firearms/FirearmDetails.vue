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

        <dt class="col-sm-4">Calibers</dt>
        <dd class="col-sm-8">
          <span v-if="firearm.calibers.length === 0">None</span>
          <span class="badge bg-info text-dark me-2" v-for="(caliber, i) in firearm.calibers" :key="i">
            {{ caliber.short_label }}
          </span>
        </dd>
      </dl>
    </div>
    <div class="col-sm-6">
      <h3>Notes</h3>
      <FirearmNotes :firearm="firearm" />
    </div>
  </div>
</template>

<script>
import FirearmNotes from 'components/firearms/FirearmNotes';
export default {
  name: 'FirearmDetails',
  components: {FirearmNotes},
  props: {
    firearm: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
