<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import MagazineForm from '@/components/magazines/MagazineForm.vue';

const router = useRouter();

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Accessories', to: { name: 'AccessoriesIndex' } },
  { label: 'Add Magazine' },
];

function onComplete(created) {
  router.push({ name: 'MagazinesShow', params: { magazine_id: created.id } });
}
</script>

<template>
  <div class="max-w-[640px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-6">Add Magazine</h1>
    <MagazineForm @complete="onComplete" @cancel="router.back()" />
  </div>
</template>
