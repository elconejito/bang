<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useLightsStore } from '@/stores/lights';

const props = defineProps({
  lightId: { type: Number, required: true },
});

const store = useLightsStore();
const light = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.lightId);
  light.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Lights', to: { name: 'AccessoriesLights' } },
  {
    label: light.value?.label ?? '…',
    to: { name: 'LightShow', params: { light_id: props.lightId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="lights"
    :entity-id="lightId"
    :crumbs="crumbs"
    :entity-title="light?.label"
  />
</template>
