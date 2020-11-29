<template>
  <div class="row">
    <div class="col table-responsive">
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th scope="col">Date</th>
            <th scope="col">Rounds</th>
            <th scope="col">Purchased</th>
            <th scope="col">Cost</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="isEmpty && !isLoading">
            <td colspan="4">
              <Empty message="No Inventory" />
            </td>
          </tr>
          <tr v-if="isLoading">
            <td colspan="4">
              <Loading />
            </td>
          </tr>
          <tr v-for="(item, i) in inventory" :key="i">
            <td>{{ item.inventory_date }}</td>
            <td>{{ item.rounds }}</td>
            <td>{{ item.purchased }}</td>
            <td>{{ item.cost }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Empty from '../Empty';
import Loading from 'components/Loading';

export default {
  name: 'InventoryList',
  components: { Loading, Empty },
  props: {
    inventory: {
      type: Array,
      required: true,
    },
    isLoading: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  computed: {
    isEmpty() {
      return this.inventory.length === 0;
    },
  },
};
</script>

<style scoped></style>
