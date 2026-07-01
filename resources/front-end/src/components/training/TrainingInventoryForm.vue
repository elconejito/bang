<template>
  <form>
    <div class="mb-4 grid grid-cols-2 gap-4">
      <div>
        <label for="rounds" class="block text-sm font-medium text-gray-700 mb-1">
          Number of rounds <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="rounds"
          name="rounds"
          placeholder="Number of Rounds"
          required
          v-model="inventory.rounds"
        />
        <p class="mt-1 text-xs text-gray-500">How many rounds are you adding or subtracting</p>
      </div>
      <div>
        <label for="inventory_date" class="block text-sm font-medium text-gray-700 mb-1">
          Date <span class="text-red-500">*</span>
        </label>
        <v-date-picker v-model="inventory.inventory_date" mode="date">
          <template #default="{ inputValue, inputEvents }">
            <input
              class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              id="inventory_date"
              :value="inputValue"
              v-on="inputEvents"
            />
          </template>
        </v-date-picker>
        <p class="mt-1 text-xs text-gray-500">When did you add this inventory?</p>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Add Entry" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { useInventoriesStore } from '@/stores/inventories';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  ammunition: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['complete']);

const inventoriesStore = useInventoriesStore();

const loading = ref(false);
const error = ref(null);
const inventory = ref({
  inventory_date: new Date(),
  rounds: '',
  is_purchase: '',
  store_id: '',
  cost: 0,
  ammunition_id: props.ammunition.id,
});

async function submit() {
  error.value = null;
  loading.value = true;
  try {
    const payload = { ...inventory.value };
    if (payload.inventory_date instanceof Date) {
      payload.inventory_date = payload.inventory_date.toISOString();
    }
    payload.is_purchase = Number(payload.is_purchase);
    await inventoriesStore.create(payload);
    emit('complete');
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>
