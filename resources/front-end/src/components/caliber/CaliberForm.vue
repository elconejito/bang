<template>
  <form>
    <div class="mb-3">
      <label for="caliber" class="form-label">Caliber<span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="caliber"
        name="caliber"
        placeholder="Name of Caliber"
        required
        v-model="caliber.caliber"
      />
      <small class="form-text text-muted">
        The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
      </small>
    </div>

    <div class="mb-3">
      <label for="label" class="form-label">Label</label>
      <input
        type="text"
        class="form-control"
        id="label"
        name="label"
        placeholder="Label of Caliber"
        v-model="caliber.label"
      />
      <small class="form-text text-muted">
        The label for the caliber which will be shown through the site, such as 9mm, 5.56, etc
      </small>
    </div>

    <div class="mb-3">
      <label for="caliber_type_id" class="form-label">Caliber Type <span class="form-required">*</span></label>
      <select
        class="form-control"
        id="caliber_type_id"
        name="caliber_type_id"
        required
        v-model="caliber.caliber_type_id"
      >
        <option v-for="(caliberType, i) in caliberTypes" :value="caliberType.id" :key="i">
          {{ caliberType.label }}
        </option>
      </select>
      <small class="form-text text-muted">
        The type of caliber such as rimfire, centerfire, or shotgun
      </small>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add New" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useReferenceStore } from '@/stores/reference'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const calibersStore = useCalibersStore()
const referenceStore = useReferenceStore()

const caliberTypes = computed(() => referenceStore.caliberType)

const loading = ref(false)
const error = ref(null)
const caliber = ref({
  caliber: '',
  label: '',
  caliber_type_id: '',
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    const { data } = await calibersStore.create(caliber.value)
    emit('complete', data)
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
