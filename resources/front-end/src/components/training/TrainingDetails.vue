<template>
  <div class="row">
    <div class="col-sm-6">
      <h3>Details</h3>
      <dl class="row">
        <dt class="col-sm-4">Location</dt>
        <dd class="col-sm-8">{{ training.location_id }}</dd>
      </dl>
    </div>
    <div class="col-sm-6">
      <h3>Notes</h3>
      NOTES
    </div>
  </div>
</template>

<script setup>
defineProps({
  training: {
    type: Object,
    required: true,
  },
})
</script>
