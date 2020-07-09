<template>
  <div class="container">
    <div class="row">
      <div class="col">
        CalibersIndex, toolbar
      </div>
    </div>
    <CaliberList :calibers="calibers" />
  </div>
</template>

<script>
import CaliberList from '../../components/Caliber/CaliberList';
export default {
  name: 'CalibersIndex',
  components: { CaliberList },
  data() {
    return {
      calibers: [],
      meta: {},
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('calibers/all').then((response) => {
        console.log('CalibersIndex fetchData() then', response);
        const { data, meta } = response;
        this.calibers = data;
        this.meta = meta || {};
      });
    },
  },
};
</script>

<style></style>
