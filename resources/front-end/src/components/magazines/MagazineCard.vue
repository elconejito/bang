<template>
  <div class="card firearm-card">
    <div class="card-header">
      <h3 class="card-title">
        {{ magazine.label }}
      </h3>
    </div>
    <div class="card-body">
      <h4>
        <span class="text-muted">{{ magazine.manufacturer }}</span><br />
        {{ magazine.model_name }}
      </h4>
      <h6>Used By:</h6>
      <p class="card-text">
        <span v-if="magazine.firearms.length === 0">None</span>
        <router-link
          class="badge bg-info text-dark me-2"
          v-for="(firearm, i) in magazine.firearms"
          :key="i"
          :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }"
        >
          {{ firearm.label }}
        </router-link>
      </p>
      <h6>Calibers Supported:</h6>
      <p class="card-text">
        <span v-if="magazine.calibers.length === 0">None</span>
        <router-link
          class="badge bg-info text-dark me-2"
          v-for="(caliber, i) in magazine.calibers"
          :key="i"
          :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
        >
          {{ caliber.short_label }}
        </router-link>
      </p>
    </div>
  </div>
</template>

<script>
import HasNumbers from 'mixins/HasNumbers';

export default {
  name: 'MagazineCard',
  mixins: [HasNumbers],
  props: {
    magazine: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
