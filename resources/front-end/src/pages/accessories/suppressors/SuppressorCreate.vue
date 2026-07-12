<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';

const router = useRouter();

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Suppressors', to: { name: 'AccessoriesSuppressors' } },
  { label: 'Add Suppressor' },
];

function onComplete(suppressor) {
  router.push({ name: 'SuppressorShow', params: { suppressor_id: suppressor.id } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Add Suppressor</h1>
    <AccessoryFormCard type="suppressor" @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
