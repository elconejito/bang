<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useLocationsStore } from '@/stores/locations';

const props = defineProps({
  locationId: { type: Number, required: true },
});

const store = useLocationsStore();
const location = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.locationId);
  location.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Locations', to: { name: 'LocationIndex' } },
  {
    label: location.value?.label ?? '…',
    to: { name: 'LocationsShow', params: { location_id: props.locationId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="locations"
    :entity-id="locationId"
    :crumbs="crumbs"
    :entity-title="location?.label"
  />
</template>
