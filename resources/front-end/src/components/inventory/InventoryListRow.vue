<template>
  <tr>
    <td>{{ inventoryDate }}</td>
    <td class="text-end">{{ rounds }}</td>
    <td class="text-end">{{ purchased }}</td>
    <td class="text-end">{{ cost }}</td>
  </tr>
</template>

<script>
import dayjs from 'dayjs';
import numeral from 'numeral';

export default {
  name: 'InventoryListRow',
  props: {
    inventory: {
      type: Object,
      required: true,
    },
  },
  computed: {
    cost() {
      return this.inventory.cost ?? '-';
    },
    inventoryDate() {
      return dayjs(this.inventory.inventory_date).format('MMM DD, YYYY');
    },
    purchased() {
      return this.inventory.purchased ? 'Yes' : 'No';
    },
    rounds() {
      return numeral(this.inventory.rounds).format('0,0');
    },
  },
};
</script>
