<template>
  <tr>
    <td>{{ inventoryDate }}</td>
    <td class="text-end">{{ rounds }}</td>
    <td class="text-end">{{ purchased }}</td>
    <td class="text-end">{{ cost }}</td>
  </tr>
</template>

<script setup>
import { computed } from 'vue'
import { useDateTimes } from '@/composables/useDateTimes'
import { useNumbers } from '@/composables/useNumbers'

const props = defineProps({
  inventory: {
    type: Object,
    required: true,
  },
})

const { ago } = useDateTimes()
const { formatQuantity } = useNumbers()

const cost = computed(() => props.inventory.order ? props.inventory.cost ?? '-' : '-')
const inventoryDate = computed(() => ago(props.inventory.inventory_date))
const purchased = computed(() => props.inventory.order ? 'Yes' : 'No')
const rounds = computed(() => formatQuantity(props.inventory.rounds))
</script>
