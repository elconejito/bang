<script setup>
import { ref, computed, onMounted } from 'vue'
import GalleryPageContent from '@/components/gallery/GalleryPageContent.vue'
import { useOpticsStore } from '@/stores/optics'

const props = defineProps({
  opticId: { type: Number, required: true },
})

const store = useOpticsStore()
const optic = ref(null)

onMounted(async () => {
  const { data } = await store.fetchOne(props.opticId)
  optic.value = data
})

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  {
    label: optic.value?.label ?? '…',
    to: { name: 'OpticShow', params: { optic_id: props.opticId } },
  },
  { label: 'Photos' },
])
</script>

<template>
  <GalleryPageContent
    entity-type="optics"
    :entity-id="opticId"
    :crumbs="crumbs"
    :entity-title="optic?.label"
  />
</template>
