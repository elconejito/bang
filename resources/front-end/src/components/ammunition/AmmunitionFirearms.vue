<template>
  <div class="row">
    <div class="col">
      <h3>Firearms</h3>
      <FirearmList :firearms="firearms" :is-loading="isLoading" />
    </div>
  </div>
</template>

<script>
import FirearmList from 'components/firearms/FirearmList';
import HasLoading from 'mixins/HasLoading';

export default {
  name: 'AmmunitionFirearms',
  components: { FirearmList },
  mixins: [HasLoading],
  props: {
    ammunition: {
      type: Object,
      required: true,
    },
    caliber: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      firearms: [],
      meta: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.fetchFirearms();
    },
    fetchFirearms() {
      this.$set(this.loadingQueue, 'firearms', false);

      const payload = {
        params: {
          search: `calibers.id:${this.caliber.id}`,
        },
      };

      this.$store
        .dispatch('firearms/all', payload)
        .then((response) => {
          console.log('AmmunitionFirearms fetchFirearms() then', response);
          const { data, meta } = response;
          this.firearms = data;
          this.meta = meta || {};
        })
        .catch((error) => {
          // show the error message
          console.error('AmmunitionFirearms fetchFirearms() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.firearms = true;
        });
    },
  },
};
</script>

<style></style>
