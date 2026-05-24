<template>
  <tr class="odd:bg-white even:bg-gray-50">
    <td class="px-4 py-3">{{ inventoryDate }}</td>
    <td class="px-4 py-3 text-right">{{ rounds }}</td>
    <td class="px-4 py-3 text-right">{{ purchased }}</td>
    <td class="px-4 py-3 text-right">{{ cost }}</td>
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
