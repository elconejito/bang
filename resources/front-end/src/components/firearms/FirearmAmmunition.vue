<template>
  <div class="row">
    <div class="col">
      <h3>Ammunition</h3>
      <AmmunitionList :ammunition="ammunition.data" :caliber="caliber" />
    </div>
  </div>
</template>

<script>
import AmmunitionList from '../../components/ammunition/AmmunitionList';
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';

export default {
  name: 'FirearmAmmunition',
  components: { AmmunitionList },
  mixins: [HasLoading, HasModal],
  props: {
    firearm: {
      type: Object,
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
    };
  },
  mounted() {
    console.log('FirearmAmmunition mounted()');
    this.fetchData();
  },
  methods: {
    fetchData() {
      console.log('FirearmAmmunition fetchData()1', this.loadingQueue);
      this.isLoading = true;
      this.$set(this.loadingQueue, 'ammunition', false);
      this.fetchAmmunition();
      console.log('FirearmAmmunition fetchData()2', this.loadingQueue);
    },
    fetchAmmunition() {
      console.log('FirearmAmmunition fetchAmmunition()');
      const payload = {
        caliberId: this.caliberId,
      };

      return this.$store.dispatch('ammunition/all', payload).then((response) => {
        console.log('FirearmAmmunition fetchData() ammunition then', response);
        const { data, meta } = response;
        this.ammunition.data = data;
        this.ammunition.meta = meta || {};

        this.loadingQueue.ammunition = true;
        // this.$set(this.loadingQueue, 'ammunition', true);
      });
    },
  },
};
</script>

<style></style>
