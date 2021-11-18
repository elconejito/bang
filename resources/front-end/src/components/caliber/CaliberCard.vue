<template>
  <div class="card caliber-card">
    <div class="card-header">
      <h3 class="card-title">
        <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }">
          {{ caliber.label }}
        </router-link>
      </h3>
      <h6 class="card-subtitle text-muted">{{ caliber.caliber_type.label }}</h6>
    </div>
    <div class="rounds">
      <div class="rounds-total">
        <span class="number">{{ formatQuantity(totalSummary.total) }}</span>
        <span class="label">TOTAL RNDS</span>
      </div>
      <div class="rounds-purpose">
        <span v-for="purpose in purposeTotals" :key="purpose">
          {{ getPurposeLabel(purpose) }}: {{ formatQuantity(totalSummary[purpose]) }}
        </span>
      </div>
    </div>
    <div class="card-body">
      <h6>Used By:</h6>
      <p class="card-text">
        <span v-if="caliber.firearms.length === 0">None</span>
        <span
          class="badge bg-info text-dark me-2"
          v-for="(firearm, i) in caliber.firearms"
          :key="i"
        >
          {{ firearm.label }}
        </span>
      </p>
    </div>
  </div>
</template>

<script>
import HasNumbers from '@/mixins/HasNumbers';
import HasLoading from 'mixins/HasLoading';
import HasPurpose from 'mixins/HasPurpose';

export default {
  name: 'CaliberCard',
  mixins: [HasLoading, HasNumbers, HasPurpose],
  props: {
    caliber: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      totalSummary: {},
    };
  },
  mounted() {
    console.log('CaliberCard mounted()');
    this.fetchData();
  },
  computed: {
    purposeTotals() {
      return Object.keys(this.totalSummary).filter((p) => p !== 'total');
    },
  },
  methods: {
    fetchData() {
      console.log('CaliberCard fetchData()1', this.loadingQueue);
      this.isLoading = true;
      this.$set(this.loadingQueue, 'caliber', false);
      this.fetchInventory();
      console.log('CaliberCard fetchData()2', this.loadingQueue);
    },
    fetchInventory() {
      console.log('CaliberCard fetchInventory()');
      const payload = {
        caliberId: this.caliber.id,
      };

      return this.$store.dispatch('calibers/total', payload).then((response) => {
        console.log('CaliberCard fetchInventory() total then', response);
        const { data } = response;
        this.totalSummary = data;

        this.loadingQueue.caliber = true;
      });
    },
  },
};
</script>

<style></style>
