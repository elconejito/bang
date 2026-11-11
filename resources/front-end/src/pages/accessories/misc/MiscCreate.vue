<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AccessoryFormCard from '@/components/accessories/AccessoryFormCard.vue';

const router = useRouter();

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Misc', to: { name: 'AccessoriesMisc' } },
  { label: 'Add Misc Item' },
];

function onComplete(misc) {
  router.push({ name: 'MiscShow', params: { misc_id: misc.id } });
}
</script>

<template>
  <div class="mx-auto max-w-[640px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Add Misc Accessory</h1>
    <AccessoryFormCard type="misc" @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
