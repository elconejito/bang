<template>
  <div class="row card-container">
    <div class="col-sm-6 col-lg-4" v-for="(caliber, i) in calibers" :key="i">
      <CaliberCard :caliber="caliber" />
    </div>
  </div>
</template>

<script>
import CaliberCard from './CaliberCard';
export default {
  name: 'CaliberList',
  components: { CaliberCard },
  props: {
    calibers: {
      type: Array,
      required: true,
    },
  },
};
</script>

<style></style>
