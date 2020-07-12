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
    <AmmunitionList :ammunition="ammunition.data" />
  </div>
</template>

<script>
import AmmunitionList from '../../components/ammunition/AmmunitionList';
export default {
  name: 'CalibersShow',
  components: { AmmunitionList },
  props: {
    caliberId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      ammunition: {
        data: [],
        meta: {},
      },
      caliber: {},
      meta: {},
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      const caliberPayload = {
        id: this.caliberId,
      };

      this.$store.dispatch('calibers/get', caliberPayload).then((response) => {
        console.log('CalibersShow fetchData() calibers then', response);
        const { data, meta } = response;
        this.caliber = data;
        this.meta = meta;
      });

      const ammunitionPayload = {
        caliber_id: this.caliberId,
      };

      this.$store.dispatch('ammunition/all', ammunitionPayload).then((response) => {
        console.log('CalibersShow fetchData() ammunition then', response);
        const { data, meta } = response;
        this.ammunition.data = data;
        this.ammunition.meta = meta || {};
      });
    },
  },
};
</script>

<style scoped></style>
