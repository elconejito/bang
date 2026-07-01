<template>
  <div class="mx-auto max-w-[640px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Calibers', to: { name: 'CalibersIndex' } },
        { label: 'Add Caliber' },
      ]"
      class="mb-4"
    />

    <h1 class="mb-6 font-display text-[28px] font-bold tracking-[-0.02em]">Add Caliber</h1>

    <div class="overflow-hidden rounded-sm border border-[#e2e4e6] bg-white">
      <CaliberForm @complete="onComplete" @cancel="router.back()" />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import CaliberForm from '@/components/caliber/CaliberForm.vue';

const router = useRouter();

function onComplete(created) {
  router.push({ name: 'CalibersShow', params: { caliber_id: created.id } });
}
</script>
