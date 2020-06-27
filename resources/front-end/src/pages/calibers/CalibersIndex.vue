<template>
  <div>
    CalibersIndex
  </div>
</template>

<script>
export default {
  name: 'CalibersIndex',
  data() {
    return {
      calibers: [],
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('calibers/all');
    },
  },
};
</script>

<style></style>
