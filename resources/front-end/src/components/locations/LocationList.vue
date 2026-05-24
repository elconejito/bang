<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="isLoading">
      <LoadingCard message="Loading Locations..." />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="hasError">
      <ErrorCard :error="error" />
    </div>
    <div class="col-sm-6 col-lg-4 mx-auto" v-if="showEmpty">
      <EmptyCard />
    </div>
    <div class="col-sm-6 col-lg-4" v-for="(location, i) in locations" :key="i" v-else>
      <LocationCard :location="location" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import LocationCard from '@/components/locations/LocationCard.vue'
import LoadingCard from '@/components/status/LoadingCard.vue'
import ErrorCard from '@/components/status/ErrorCard.vue'
import EmptyCard from '@/components/status/EmptyCard.vue'

const props = defineProps({
  locations: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: [Error, Boolean],
    default: false,
  },
})

const hasError = computed(() => props.error !== false)
const showEmpty = computed(() => props.locations.length === 0 && !props.isLoading && props.error === false)
</script>
