<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';
import { useOpticsStore } from '@/stores/optics';

const props = defineProps({
  opticId: { type: Number, required: true },
});

const router = useRouter();
const opticsStore = useOpticsStore();
const optic = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await opticsStore.fetchOne(props.opticId);
  optic.value = data;
  loading.value = false;
});

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Optics', to: { name: 'AccessoriesOptics' } },
  { label: 'Edit Optic' },
];

function onComplete(updated) {
  router.push({ name: 'OpticShow', params: { optic_id: updated.id } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Edit Optic</h1>
    <div v-if="loading" class="text-sm text-muted py-6">Loading…</div>
    <AccessoryFormCard
      v-else
      type="optic"
      :item="optic"
      @complete="onComplete"
      @cancel="router.back()"
    />
  </div>
</template>
