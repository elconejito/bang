<template>
  <div class="row">
    <div class="col-sm-6">
      <h3>Details</h3>
      <dl class="row">
        <dt class="col-sm-4">Manufacturer</dt>
        <dd class="col-sm-8">{{ ammunition.manufacturer }}</dd>

        <dt class="col-sm-4">Label</dt>
        <dd class="col-sm-8">{{ ammunition.label }}</dd>

        <dt class="col-sm-4">Purpose</dt>
        <dd class="col-sm-8">{{ purposeLabel }}</dd>

        <!-- Shotgun -->
        <template v-if="caliber.caliber_type_id === 3">
          <dt class="col-sm-4">Shell Type</dt>
          <dd class="col-sm-8">{{ shellTypeLabel }}</dd>

          <dt class="col-sm-4">Weight (oz)</dt>
          <dd class="col-sm-8">{{ ammunition.weight }}</dd>

          <dt class="col-sm-4">Shell Length</dt>
          <dd class="col-sm-8">{{ shellLengthLabel }}</dd>

          <dt class="col-sm-4">Shot Material</dt>
          <dd class="col-sm-8">{{ shotMaterialLabel }}</dd>
        </template>

        <!-- Not a Shotgun -->
        <template v-else >
          <dt class="col-sm-4">Bullet Type</dt>
          <dd class="col-sm-8">{{ bulletTypeLabel }}</dd>

          <dt class="col-sm-4">Weight (gr)</dt>
          <dd class="col-sm-8">{{ ammunition.weight }}</dd>

          <dt class="col-sm-4">Case Material</dt>
          <dd class="col-sm-8">{{ ammunitionCasingLabel }}</dd>

          <dt class="col-sm-4">Condition</dt>
          <dd class="col-sm-8">{{ ammunitionConditionLabel }}</dd>

          <dt class="col-sm-4">Primer Type</dt>
          <dd class="col-sm-8">{{ primerTypeLabel }}</dd>
        </template>
      </dl>
    </div>
    <div class="col-sm-6">
      <h3>Notes</h3>
    </div>
  </div>
</template>

<script>
import AmmunitionHelper from 'mixins/AmmunitionHelper';

export default {
  name: 'AmmunitionDetails',
  mixins: [AmmunitionHelper],
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

<style></style>
