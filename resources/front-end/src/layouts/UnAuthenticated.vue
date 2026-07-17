<template>
  <div class="relative min-h-dvh bg-gray-50 pb-[60px]">
    <TopNavigation />
    <main class="flex min-h-[calc(100dvh-7.25rem)] items-center justify-center px-4 py-6 sm:px-6">
      <router-view />
    </main>
    <SiteFooter />
  </div>
</template>

<script setup>
import TopNavigation from '@/components/navigation/TopNavigation.vue';
import SiteFooter from '@/components/SiteFooter.vue';
</script>
