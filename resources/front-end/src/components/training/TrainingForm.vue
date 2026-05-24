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
        v-model="training.label"
      />
    </div>

    <div class="mb-4">
      <label for="session_date" class="block text-sm font-medium text-gray-700 mb-1">
        Date <span class="text-red-500">*</span>
      </label>
      <v-date-picker v-model="training.session_date" mode="date">
        <template #default="{ inputValue, inputEvents }">
          <input
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            id="session_date"
            :value="inputValue"
            v-on="inputEvents"
          />
        </template>
      </v-date-picker>
      <p class="mt-1 text-xs text-gray-500">When did this training session take place?</p>
    </div>

    <div class="mb-4">
      <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
      <textarea
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 resize-y"
        id="description"
        name="description"
        rows="3"
        v-model="training.description"
      ></textarea>
    </div>

    <div class="mb-4">
      <label for="location_id" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
      <select
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="location_id"
        name="location_id"
        v-model="training.location_id"
      >
        <option value="">- Select One -</option>
        <option v-for="(location, i) in locations" :value="location.id" :key="i">
          {{ location.label }}
        </option>
      </select>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
      <ActionButton text="Add New" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTrainingStore } from '@/stores/training'
import { useLocationsStore } from '@/stores/locations'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const trainingStore = useTrainingStore()
const locationsStore = useLocationsStore()

const loading = ref(false)
const error = ref(null)
const locations = ref([])
const training = ref({
  label: '',
  description: '',
  session_date: '',
  location_id: '',
})

onMounted(async () => {
  const { data } = await locationsStore.fetchAll()
  locations.value = data
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    const { data } = await trainingStore.create(training.value)
    emit('complete', data)
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
