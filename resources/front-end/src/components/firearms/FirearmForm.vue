<template>
  <form>
    <div class="mb-4 grid grid-cols-3 gap-4">
      <div>
        <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">
          Manufacturer <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="manufacturer"
          name="manufacturer"
          placeholder="Manufacturer"
          required
          v-model="firearm.manufacturer"
        />
        <p class="mt-1 text-xs text-gray-500">The name of the manufacturer</p>
      </div>
      <div class="col-span-2">
        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">
          Model <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          id="model"
          name="model"
          placeholder="Model"
          required
          v-model="firearm.model"
        />
        <p class="mt-1 text-xs text-gray-500">The Model or Version of the firearm</p>
      </div>
    </div>

    <div class="mb-4">
      <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
        Label <span class="text-red-500">*</span>
      </label>
      <input
        type="text"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="label"
        name="label"
        placeholder="Label of Firearm"
        required
        v-model="firearm.label"
      />
      <p class="mt-1 text-xs text-gray-500">The label that will show throughout the app</p>
    </div>

    <div class="mb-4">
      <div class="mb-1 flex items-center justify-between">
        <label class="block text-sm font-medium text-gray-700">Calibers</label>
        <button
          type="button"
          class="inline-flex items-center gap-1 text-xs font-medium text-brass-800 transition-colors hover:text-brass-600"
          @click="showCaliberModal = true"
        >
          <Plus class="h-3.5 w-3.5" /> Add caliber
        </button>
      </div>
      <p class="mb-2 text-xs text-gray-500">Check all the calibers this firearm supports</p>
      <div class="grid grid-cols-2 gap-1">
        <label
          v-for="(caliber, i) in calibers"
          :key="i"
          :for="`check-${i}`"
          class="flex items-center gap-2 text-sm"
        >
          <input
            class="h-4 w-4 rounded border-gray-300 text-blue-600"
            type="checkbox"
            :value="caliber.id"
            :id="`check-${i}`"
            v-model="firearm.calibers"
          />
          {{ caliber.label }}
        </label>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Add New" :is-loading="loading" variant="primary" @click="submit" />
    </div>

    <ReferenceItemModal
      v-if="showCaliberModal"
      type="caliber"
      mode="add"
      @close="showCaliberModal = false"
      @saved="onCaliberSaved"
    />
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus } from 'lucide-vue-next';
import { useFirearmsStore } from '@/stores/firearms';
import { useCalibersStore } from '@/stores/calibers';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';
import ReferenceItemModal from '@/components/reference/ReferenceItemModal.vue';

const emit = defineEmits(['complete']);

const firearmsStore = useFirearmsStore();
const calibersStore = useCalibersStore();

const loading = ref(false);
const error = ref(null);
const calibers = ref([]);
const showCaliberModal = ref(false);
const firearm = ref({
  label: '',
  manufacturer: '',
  model: '',
  calibers: [],
});

onMounted(async () => {
  const { data } = await calibersStore.fetchAll();
  calibers.value = data;
});

async function onCaliberSaved(caliber) {
  showCaliberModal.value = false;
  const { data } = await calibersStore.fetchAll();
  calibers.value = data;
  if (caliber?.id && !firearm.value.calibers.includes(caliber.id)) {
    firearm.value.calibers.push(caliber.id);
  }
}

async function submit() {
  error.value = null;
  loading.value = true;
  try {
    const { data } = await firearmsStore.create(firearm.value);
    emit('complete', data);
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>
