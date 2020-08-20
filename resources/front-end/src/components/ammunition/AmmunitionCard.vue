<template>
  <div class="card ammunition-card">
    <div class="card-header">
      <h3 class="card-title">
        <router-link
          :to="{
            name: 'AmmunitionShow',
            params: { caliber_id: caliber.id, ammunition_id: ammunition.id },
          }"
        >
          {{ ammunition.label }}
        </router-link>
      </h3>
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
  </div>
</template>

<script>
import HasNumbers from '../../mixins/HasNumbers';
export default {
  name: 'AmmunitionCard',
  mixins: [HasNumbers],
  props: {
    ammunition: {
      type: Object,
      required: true,
    },
    caliber: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped></style>
