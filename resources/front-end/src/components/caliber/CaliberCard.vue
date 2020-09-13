<template>
  <div class="card caliber-card">
    <div class="card-header">
      <h3 class="card-title">
        <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }">
          {{ caliber.label }}
        </router-link>
      </h3>
      <h6 class="card-subtitle text-muted">{{ caliber.caliber_type.label }}</h6>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span class="number">{{ formatQuantity(5000) }}</span>
        <span class="label">TOTAL RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span>HD: {{ formatQuantity(1000) }}</span>
        <span>Range: {{ formatQuantity(10000) }}</span>
        <span>Match: {{ formatQuantity(500) }}</span>
      </div>
    </div>
    <div class="card-body">
      <h6>Used By:</h6>
      <p class="card-text">
        <span v-if="caliber.firearms.length === 0">None</span>
        <span class="badge badge-info mr-2" v-for="(firearm, i) in caliber.firearms" :key="i">
          {{ firearm.label }}
        </span>
      </p>
    </div>
  </div>
</template>

<script>
import HasNumbers from '../../mixins/HasNumbers';

export default {
  name: 'CaliberCard',
  mixins: [HasNumbers],
  props: {
    caliber: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style></style>
