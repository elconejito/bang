<script setup>
import { ref, computed, onMounted } from 'vue';
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue';
import { useAmmunitionStore } from '@/stores/ammunition';

const props = defineProps({
  ammunitionId: { type: Number, required: true },
});

const store = useAmmunitionStore();
const ammo = ref(null);

onMounted(async () => {
  const { data } = await store.fetchOne(props.ammunitionId);
  ammo.value = data;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Ammo', to: { name: 'AmmoIndex' } },
  {
    label: ammo.value?.label ?? '…',
    to: { name: 'AmmoShow', params: { ammunition_id: props.ammunitionId } },
  },
  { label: 'Photos' },
]);
</script>

<template>
  <GalleryPageContent
    entity-type="ammunition"
    :entity-id="ammunitionId"
    :crumbs="crumbs"
    :entity-title="ammo?.label"
  />
</template>
