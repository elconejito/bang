<template>
  <div class="card firearm-card">
    <div class="card-header">
      <h3 class="card-title">
        {{ training.label }}
      </h3>
    </div>
    <div class="card-body">
      <p>{{ training.session_date }}</p>
      <p>Desc: {{ training.description }}</p>
      <p>Location: {{ training.location_id }}</p>
      <p>
        <router-link :to="{ name: 'TrainingShow', params: { training_id: training.id } }">
          LINK
        </router-link>
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TrainingCard',
  props: {
    training: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
