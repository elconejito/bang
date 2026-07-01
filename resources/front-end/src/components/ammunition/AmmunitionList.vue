<template>
  <div>
    <div v-if="ammunition.length === 0">
      <Empty message="No ammo loads yet" />
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <AmmunitionCard
        v-for="(ammo, i) in ammunition"
        :key="i"
        :ammunition="ammo"
        :caliber="caliber"
      />
    </div>
  </div>
</template>

<script setup>
import AmmunitionCard from '@/components/ammunition/AmmunitionCard.vue';
import Empty from '@/components/Empty.vue';

defineProps({
  ammunition: {
    type: Array,
    required: true,
  },
  caliber: {
    type: Object,
    required: true,
  },
});
</script>
