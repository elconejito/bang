<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useSuppressorsStore } from '@/stores/suppressors';

const props = defineProps({
  suppressorId: { type: Number, required: true },
});

const store = useSuppressorsStore();
const suppressor = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.suppressorId);
  suppressor.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  {
    label: suppressor.value?.label ?? '…',
    to: { name: 'SuppressorShow', params: { suppressor_id: props.suppressorId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="suppressors"
    :entity-id="suppressorId"
    :crumbs="crumbs"
    :entity-title="suppressor?.label"
  />
</template>
