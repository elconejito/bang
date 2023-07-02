<template>
  <div class="card firearm-card">
    <div class="card-header">
      <h3 class="card-title">
        <router-link :to="{ name: 'FirearmsShow', params: { firearm_id: firearm.id } }">
          {{ firearm.label }}
        </router-link>
      </h3>
    </div>
    <div class="card-body">
      <h4>
        <span class="text-muted">{{ firearm.manufacturer }}</span><br />
        {{ firearm.model }}
      </h4>
      <h6>Calibers Supported:</h6>
      <p class="card-text">
        <span v-if="firearm.calibers.length === 0">None</span>
        <router-link
          class="badge bg-info text-dark me-2"
          v-for="(caliber, i) in firearm.calibers"
          :key="i"
          :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }"
        >
          {{ caliber.label }}
        </router-link>
      </p>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span class="number" :title="formatQuantity(5000)">
          {{ formatSmartQuantity(5000) }}
        </span>
        <span class="label">RNDS FIRED</span>
      </div>
    </div>
  </div>
</template>

<script>
import HasNumbers from '../../mixins/HasNumbers';

export default {
  name: 'FirearmCard',
  mixins: [HasNumbers],
  props: {
    firearm: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
