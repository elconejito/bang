<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';

const router = useRouter();

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Lights', to: { name: 'AccessoriesLights' } },
  { label: 'Add Light' },
];

function onComplete(light) {
  router.push({ name: 'LightShow', params: { light_id: light.id } });
}
</script>

<template>
  <div class="mx-auto max-w-[640px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Add Light</h1>
    <AccessoryFormCard type="light" @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
