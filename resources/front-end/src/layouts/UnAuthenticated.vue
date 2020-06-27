<template>
  <div>
    <TopNavigation />
    <div class="container">
      <router-view />
    </div>
    <SiteFooter />
  </div>
</template>

<script>
import TopNavigation from '../components/navigation/TopNavigation';
import SiteFooter from '../components/SiteFooter';

export default {
  name: 'UnAuthenticated',
  components: { SiteFooter, TopNavigation },
};
</script>

<style></style>
