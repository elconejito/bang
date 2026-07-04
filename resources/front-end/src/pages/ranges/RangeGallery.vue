<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useRangesStore } from '@/stores/ranges';

const props = defineProps({
  rangeId: { type: Number, required: true },
});

const store = useRangesStore();
const range = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.rangeId);
  range.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Ranges', to: { name: 'RangesIndex' } },
  {
    label: range.value?.label ?? '…',
    to: { name: 'RangesShow', params: { range_id: props.rangeId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="ranges"
    :entity-id="rangeId"
    :crumbs="crumbs"
    :entity-title="range?.label"
  />
</template>
