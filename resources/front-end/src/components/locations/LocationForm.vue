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
        v-model="location.label"
      />
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="location.description"
      ></textarea>
    </div>

    <div class="form-group">
      <label for="location_type_id">Location Type</label>
      <select class="form-control" id="location_type_id" name="location_type_id" required v-model="location.location_type_id">
        <option v-for="(locationType, i) in locationTypes" :value="locationType.id" :key="i">
          {{ locationType.label }}
        </option>
      </select>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useLocationsStore } from '@/stores/locations'
import { useReferenceStore } from '@/stores/reference'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const locationsStore = useLocationsStore()
const referenceStore = useReferenceStore()

const locationTypes = computed(() => referenceStore.locationType)

const loading = ref(false)
const error = ref(null)
const location = ref({
  label: '',
  description: '',
  location_type_id: '',
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await locationsStore.create(location.value)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
