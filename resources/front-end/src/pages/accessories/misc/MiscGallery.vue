<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useMiscAccessoriesStore } from '@/stores/miscAccessories';

const props = defineProps({
  miscId: { type: Number, required: true },
});

const store = useMiscAccessoriesStore();
const misc = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.miscId);
  misc.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  {
    label: misc.value?.label ?? '…',
    to: { name: 'MiscShow', params: { misc_id: props.miscId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="misc-accessories"
    :entity-id="miscId"
    :crumbs="crumbs"
    :entity-title="misc?.label"
  />
</template>
