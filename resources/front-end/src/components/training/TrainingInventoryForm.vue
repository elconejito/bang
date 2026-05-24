<template>
  <form>
    <div class="row">
      <div class="col">
        <label for="rounds">Number of rounds <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="rounds"
          name="rounds"
          placeholder="Number of Rounds"
          required
          v-model="inventory.rounds"
        />
        <small class="form-text text-muted">
          How many rounds are you adding or subtracting to your inventory
        </small>
      </div>
      <div class="col">
        <label for="inventory_date">Date <span class="form-required">*</span></label>
        <v-date-picker v-model="inventory.inventory_date" mode="date">
          <template #default="{ inputValue, inputEvents }">
            <input
              class="form-control"
              id="inventory_date"
              :value="inputValue"
              v-on="inputEvents"
            />
          </template>
        </v-date-picker>
        <small class="form-text text-muted">
          When did you add this inventory?
        </small>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useInventoriesStore } from '@/stores/inventories'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['complete'])

const inventoriesStore = useInventoriesStore()

const loading = ref(false)
const error = ref(null)
const inventory = ref({
  inventory_date: new Date(),
  rounds: '',
  is_purchase: '',
  store_id: '',
  cost: 0,
  ammunition_id: props.ammunition.id,
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    const payload = { ...inventory.value }
    if (payload.inventory_date instanceof Date) {
      payload.inventory_date = payload.inventory_date.toISOString()
    }
    payload.is_purchase = Number(payload.is_purchase)
    await inventoriesStore.create(payload)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
