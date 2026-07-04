<template>
  <div>
    <TopNavigation />
    <div class="flex flex-col items-center justify-center py-24">
      <p class="text-gray-500">Not found</p>
    </div>
    <SiteFooter />
  </div>
</template>

<script setup>
import TopNavigation from '@/components/navigation/TopNavigation.vue';
import SiteFooter from '@/components/SiteFooter.vue';
</script>
