<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';
import { useSuppressorsStore } from '@/stores/suppressors';

const props = defineProps({
  suppressorId: { type: Number, required: true },
});

const router = useRouter();
const suppressorsStore = useSuppressorsStore();

const suppressor = ref(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await suppressorsStore.fetchOne(props.suppressorId);
  suppressor.value = data;
  loading.value = false;
});

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Edit Suppressor' },
];

function onComplete(updated) {
  router.push({ name: 'SuppressorShow', params: { suppressor_id: updated.id } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Edit Suppressor</h1>
    <div v-if="loading" class="text-sm text-muted py-6">Loading…</div>
    <AccessoryFormCard v-else type="suppressor" :item="suppressor" @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
