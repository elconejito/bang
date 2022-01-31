<template>
  <div class="card ammunition-card">
    <div class="card-header">
      <h3 class="card-title">
        <small>{{ ammunition.manufacturer }}</small><br />
        <router-link
          :to="{
            name: 'AmmunitionShow',
            params: { caliber_id: caliber.id, ammunition_id: ammunition.id },
          }"
        >
          {{ ammunition.label }}
        </router-link>
      </h3>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span v-if="isLoading" class="number">
          <Loading />
        </span>
        <span v-else class="number" :title="formatQuantity(rounds)">
          {{ formatSmartQuantity(rounds) }}
        </span>
        <span class="label">RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span>{{ purposeLabel }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import HasLoading from '../../mixins/HasLoading';
import HasNumbers from '../../mixins/HasNumbers';
import Loading from '../../components/Loading';

export default {
  name: 'AmmunitionCard',
  components: { Loading },
  mixins: [HasLoading, HasNumbers],
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
      rounds: 0,
    };
  },
  computed: {
    purposeLabel() {
      return this.ammunition.purpose ? this.ammunition.purpose.label : '';
    },
  },
  mounted() {
    console.log('AmmunitionCard mounted()');
    this.fetchData();
  },
  methods: {
    fetchData() {
      console.log('AmmunitionCard fetchData()1', this.loadingQueue);
      this.isLoading = true;
      this.$set(this.loadingQueue, 'inventory', false);
      this.fetchInventory();
      console.log('AmmunitionCard fetchData()2', this.loadingQueue);
    },
    fetchInventory() {
      console.log('AmmunitionCard fetchInventory()');
      const payload = {
        ammunitionId: this.ammunition.id,
      };

      return this.$store.dispatch('ammunition/total', payload).then((response) => {
        console.log('AmmunitionCard fetchInventory() total then', response);
        const { data } = response;
        this.rounds = data.total;

        this.loadingQueue.inventory = true;
        // this.$set(this.loadingQueue, 'ammunition', true);
      });
    },
  },
};
</script>

<style scoped></style>
