<template>
  <div>
    <TopNavigation />
    <div class="container mx-auto px-4 py-8">
      <router-view />
    </div>
    <SiteFooter />
  </div>
</template>

<script setup>
import TopNavigation from '@/components/navigation/TopNavigation.vue'
import SiteFooter from '@/components/SiteFooter.vue'
</script>
