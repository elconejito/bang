<template>
  <form>
    <div class="form-group">
      <label for="label">Label <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="label" name="label" placeholder="Label of Caliber" required v-model="caliber.label">
      <small class="form-text text-muted">
        The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
      </small>
    </div>

    <div class="form-group">
      <label for="short_label">Short Label <span class="form-required">*</span></label>
      <input type="text" class="form-control" id="short_label" name="short_label" placeholder="Short Label for Caliber" required v-model="caliber.short_label">
      <small class="form-text text-muted">
        The label used throughout the app
      </small>
    </div>

    <div class="form-group">
      <label for="caliber_type_id">Caliber Type <span class="form-required">*</span></label>
      <select class="form-control" id="caliber_type_id" name="caliber_type_id" required v-model="caliber.caliber_type_id">
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
      <ActionButton text="Save Changes" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted, toRef } from 'vue'
import { useCalibersStore } from '@/stores/calibers'
import { useReferenceStore } from '@/stores/reference'
import { useForm } from '@/composables/useForm'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const props = defineProps({
  original: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['complete'])

const calibersStore = useCalibersStore()
const referenceStore = useReferenceStore()
const { initData } = useForm()

const caliberTypes = computed(() => referenceStore.caliberType)

const loading = ref(false)
const error = ref(null)
const caliber = ref({
  id: '',
  label: '',
  short_label: '',
  caliber_type_id: '',
})

onMounted(() => {
  initData(caliber, toRef(props, 'original'))
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await calibersStore.update(caliber.value.id, caliber.value)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
