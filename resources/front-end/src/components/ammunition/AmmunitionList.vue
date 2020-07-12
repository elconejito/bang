<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4" v-for="(ammo, i) in ammunition" :key="i">
      <AmmunitionCard :ammunition="ammo" />
    </div>
  </div>
</template>

<script>
import AmmunitionCard from './AmmunitionCard';
export default {
  name: 'AmmunitionList',
  components: { AmmunitionCard },
  props: {
    ammunition: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped></style>
