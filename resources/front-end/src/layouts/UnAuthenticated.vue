<template>
  <div>
    <TopNavigation />
    <div class="container">
      <router-view />
    </div>
    <SiteFooter />
  </div>
</template>

<script setup>
import TopNavigation from '@/components/navigation/TopNavigation.vue'
import SiteFooter from '@/components/SiteFooter.vue'
</script>

<style></style>
