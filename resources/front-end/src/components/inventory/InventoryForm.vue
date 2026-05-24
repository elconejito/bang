<template>
  <form>
    <div class="form-group">
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

    <div class="form-group">
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
        When did you add or remove this inventory?
      </small>
    </div>

    <div class="form-check">
      <input
        class="form-check-input"
        type="checkbox"
        value="1"
        id="is-purchase"
        v-model="inventory.is_purchase"
        true-value="1"
        false-value="0"
      />
      <label class="form-check-label" for="is-purchase">
        Was this a purchase?
      </label>
    </div>

    <template v-if="inventory.is_purchase === '1'">
      <div class="form-group">
        <label for="store" class="form-label">Choose the store</label>
        <select class="form-select" id="store" name="store" v-model="inventory.store_id">
          <option selected>- Select One -</option>
          <option v-for="(store, i) in stores" :value="store.id" :key="i">{{ store.label }}</option>
        </select>
      </div>

      <div class="form-group">
        <label for="cost" class="form-label">Amount</label>
        <input
          type="text"
          class="form-control"
          id="cost"
          name="cost"
          required
          v-model="inventory.cost"
        />
      </div>
    </template>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInventoriesStore } from '@/stores/inventories'
import { useGunStoresStore } from '@/stores/gunStores'
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
const gunStoresStore = useGunStoresStore()

const loading = ref(false)
const error = ref(null)
const stores = ref([])
const inventory = ref({
  inventory_date: new Date(),
  rounds: '',
  is_purchase: '',
  store_id: '',
  cost: 0,
  ammunition_id: props.ammunition.id,
})

onMounted(async () => {
  const { data } = await gunStoresStore.fetchAll()
  stores.value = data
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
