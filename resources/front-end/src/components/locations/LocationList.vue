<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <LoadingCard message="Loading Locations..." />
    </div>
    <div v-else-if="hasError" class="flex justify-center py-12">
      <ErrorCard :error="error" />
    </div>
    <div v-else-if="showEmpty">
      <EmptyCard
        title="No storage locations yet"
        message="Add safes, rooms, or cases so firearms and accessories have a place."
        action-label="Add Location"
        :action-to="{ name: 'LocationsCreate' }"
      />
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <LocationCard v-for="(location, i) in locations" :key="i" :location="location" />
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
