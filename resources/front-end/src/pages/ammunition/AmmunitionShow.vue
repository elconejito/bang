<template>
  <div class="container">
    <div class="row">
      <div class="col">
        CalibersShow, toolbar
      </div>
    </div>
    <div class="row">
      <div class="col">
        <h1>{{ caliber.label }}</h1>
        <p class="text-muted">{{ caliber.caliber_type.label }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AmmunitionShow',
  props: {
    ammunitionId: {
      type: Number,
      required: true,
    },
    caliberId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      ammunition: {},
      caliber: {},
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      const caliberPayload = {
        caliberId: this.caliberId,
      };

      this.$store.dispatch('ammunition/get', caliberPayload).then((response) => {
        console.log('AmmunitionShow fetchData() calibers then', response);
        const { data } = response;
        this.caliber = data;
      });

      const ammunitionPayload = {
        ammunitionId: this.ammunitionId,
        caliberId: this.caliberId,
      };

      this.$store.dispatch('ammunition/get', ammunitionPayload).then((response) => {
        console.log('AmmunitionShow fetchData() ammunition then', response);
        const { data } = response;
        this.ammunition = data;
      });
    },
  },
};
</script>

<style scoped></style>
