<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import TrainingForm from '@/components/training/TrainingForm.vue';

const router = useRouter();

const crumbs = [
  { label: 'Home', to: '/' },
  { label: 'Training', to: { name: 'TrainingIndex' } },
  { label: 'Log Session' },
];

function onComplete(created) {
  router.push({ name: 'TrainingShow', params: { training_id: created?.id } });
}
</script>

<template>
  <div class="max-w-[760px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <h1 class="font-display font-bold text-[28px] tracking-[-0.02em] mb-1">Log Session</h1>
    <p class="mb-6 text-[15px] text-ink-500">Add a firearm line for each gun you shot. Rounds and ammo will be applied to your inventory.</p>
    <TrainingForm @complete="onComplete" />
  </div>
</template>
