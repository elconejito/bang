<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="ammunition.length === 0">
      <Empty message="No ammo" />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(ammo, i) in ammunition" :key="i">
      <AmmunitionCard :ammunition="ammo" :caliber="caliber" />
    </div>
  </div>
</template>

<script setup>
import AmmunitionCard from '@/components/ammunition/AmmunitionCard.vue'
import Empty from '@/components/Empty.vue'

defineProps({
  ammunition: {
    type: Array,
    required: true,
  },
  caliber: {
    type: Object,
    required: true,
  },
})
</script>
