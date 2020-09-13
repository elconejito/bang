<template>
  <div>
    <TopNavigation />
    <div class="container">
      <div class="row">
        <div class="col text-center">Not found</div>
      </div>
    </div>
    <SiteFooter />
  </div>
</template>

<script>
import TopNavigation from '../../components/navigation/TopNavigation';
import SiteFooter from '../../components/SiteFooter';

export default {
  name: 'NotFound404',
  components: { SiteFooter, TopNavigation },
};
</script>

<style></style>
