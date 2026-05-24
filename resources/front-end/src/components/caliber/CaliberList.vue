<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Calibers..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isEmpty && !isLoading">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(caliber, i) in calibers" :key="i" v-else>
      <CaliberCard :caliber="caliber" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import CaliberCard from '@/components/caliber/CaliberCard.vue'
import LoadingCard from '@/components/status/LoadingCard.vue'
import EmptyCard from '@/components/status/EmptyCard.vue'

const props = defineProps({
  calibers: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const isEmpty = computed(() => props.calibers.length === 0)
</script>
