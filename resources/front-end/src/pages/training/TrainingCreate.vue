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
  <div class="mx-auto max-w-[760px] px-4 py-6 pb-16 sm:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />
    <h1 class="mb-1 font-display text-[28px] font-bold tracking-[-0.02em]">Log Session</h1>
    <p class="mb-[22px] text-[15px] text-[#6b7077]">
      Add a firearm line for each gun you shot. Bang updates your ammo and round counts
      automatically. Targets get added later from the session page.
    </p>
    <TrainingForm @complete="onComplete" />
  </div>
</template>
