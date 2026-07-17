<template>
  <div class="mx-auto max-w-[640px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[
        { label: 'Home', to: '/' },
        { label: 'Firearms', to: { name: 'FirearmsIndex' } },
        { label: 'Add Firearm' },
      ]"
      class="mb-4"
    />

    <PageHeader title="Add Firearm" class="mb-1" />
    <p class="mb-6 text-[15px] text-[#6b7077]">
      The essentials only — these rarely change. Photos, accessories &amp; range logs are added from
      the firearm's page after you save.
    </p>

    <FirearmFormCard @complete="onComplete" @cancel="router.push({ name: 'FirearmsIndex' })" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import PageHeader from '@/components/PageHeader.vue';
import FirearmFormCard from '@/components/firearms/FirearmFormCard.vue';

const router = useRouter();

function onComplete(created) {
  router.push({ name: 'FirearmsShow', params: { firearm_id: created.id } });
}
</script>
