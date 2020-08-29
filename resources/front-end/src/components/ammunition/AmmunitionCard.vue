<template>
  <div class="card ammunition-card">
    <div class="card-header">
      <h3 class="card-title">
        <small>{{ ammunition.manufacturer }}</small><br />
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
        <span class="label">RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span>{{ purposeLabel }}</span>
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
  computed: {
    purposeLabel() {
      return this.ammunition.purpose ? this.ammunition.purpose.label : '';
    },
  },
};
</script>

<style scoped></style>
