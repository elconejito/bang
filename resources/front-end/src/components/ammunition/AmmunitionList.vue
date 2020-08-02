<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="ammunition">
      <Empty message="No ammo" />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(ammo, i) in ammunition" :key="i">
      <AmmunitionCard :ammunition="ammo" />
    </div>
  </div>
</template>

<script>
import AmmunitionCard from './AmmunitionCard';
import Empty from '../Empty';

export default {
  name: 'AmmunitionList',
  components: { Empty, AmmunitionCard },
  props: {
    ammunition: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style scoped></style>
