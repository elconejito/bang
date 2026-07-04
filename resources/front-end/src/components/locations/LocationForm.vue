<template>
  <form>
    <div class="mb-4">
      <label for="label" class="block text-sm font-medium text-gray-700 mb-1">
        Label <span class="text-red-500">*</span>
      </label>
      <input
        type="text"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="label"
        name="label"
        required
        v-model="location.label"
      />
    </div>

    <div class="mb-4">
      <label for="description" class="block text-sm font-medium text-gray-700 mb-1"
        >Description</label
      >
      <textarea
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-y"
        id="description"
        name="description"
        rows="3"
        v-model="location.description"
      ></textarea>
    </div>

    <div class="mb-4">
      <label for="location_type_id" class="block text-sm font-medium text-gray-700 mb-1"
        >Location Type</label
      >
      <select
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="location_type_id"
        name="location_type_id"
        required
        v-model="location.location_type_id"
      >
        <option v-for="(locationType, i) in locationTypes" :value="locationType.id" :key="i">
          {{ locationType.label }}
        </option>
      </select>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Add Entry" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useLocationsStore } from '@/stores/locations';
import { useReferenceStore } from '@/stores/reference';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const emit = defineEmits(['complete']);

const locationsStore = useLocationsStore();
const referenceStore = useReferenceStore();

const locationTypes = computed(() => referenceStore.locationType);

const loading = ref(false);
const error = ref(null);
const location = ref({
  label: '',
  description: '',
  location_type_id: '',
});

async function submit() {
  error.value = null;
  loading.value = true;
  try {
    await locationsStore.create(location.value);
    emit('complete');
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors;
    error.value = err;
  } finally {
    loading.value = false;
  }
}
</script>
