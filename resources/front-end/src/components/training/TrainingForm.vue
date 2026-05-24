<template>
  <form>
    <div class="form-group">
      <label for="label">Label <span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="label"
        name="label"
        required
        v-model="training.label"
      />
    </div>

    <div class="form-group">
      <label for="session_date">Date <span class="form-required">*</span></label>
      <v-date-picker v-model="training.session_date" mode="date">
        <template #default="{ inputValue, inputEvents }">
          <input class="form-control" id="session_date" :value="inputValue" v-on="inputEvents" />
        </template>
      </v-date-picker>
      <small class="form-text text-muted">When did you add or remove this inventory?</small>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="training.description"
      ></textarea>
    </div>

    <div class="form-group">
      <label for="location_id" class="form-label">Choose the location</label>
      <select class="form-select" id="location_id" name="location_id" v-model="training.location_id">
        <option selected>- Select One -</option>
        <option v-for="(location, i) in locations" :value="location.id" :key="i">
          {{ location.label }}
        </option>
      </select>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
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
