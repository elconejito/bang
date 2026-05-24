<template>
  <form>
    <div class="form-group row">
      <div class="col-sm-4">
        <label for="manufacturer">Manufacturer <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="manufacturer"
          name="manufacturer"
          placeholder="Manufacturer"
          required
          v-model="firearm.manufacturer"
        />
        <small class="form-text text-muted">
          The name of the manufacturer
        </small>
      </div>
      <div class="col-sm-8">
        <label for="model">Model <span class="form-required">*</span></label>
        <input
          type="text"
          class="form-control"
          id="model"
          name="model"
          placeholder="Model"
          required
          v-model="firearm.model"
        />
        <small class="form-text text-muted">
          The Model or Version of the firearm
        </small>
      </div>
    </div>

    <div class="form-group">
      <label for="label">Label <span class="form-required">*</span></label>
      <input
        type="text"
        class="form-control"
        id="label"
        name="label"
        placeholder="Label of Firearm"
        required
        v-model="firearm.label"
      />
      <small class="form-text text-muted">
        The label that will show throughout the app
      </small>
    </div>

    <div class="form-group row">
      <div class="col-12">
        <label>Calibers</label>
        <small class="form-text text-muted">
          Check all the calibers this firearm supports
        </small>
      </div>
      <div class="col-sm-6" v-for="(caliber, i) in calibers" :key="i">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" :value="caliber.id" :id="`check-${i}`" v-model="firearm.calibers">
          <label class="form-check-label" :for="`check-${i}`">
            {{ caliber.label }}
          </label>
        </div>
      </div>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add New" :is-loading="loading" class="btn-primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFirearmsStore } from '@/stores/firearms'
import { useCalibersStore } from '@/stores/calibers'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const firearmsStore = useFirearmsStore()
const calibersStore = useCalibersStore()

const loading = ref(false)
const error = ref(null)
const calibers = ref([])
const firearm = ref({
  label: '',
  manufacturer: '',
  model: '',
  calibers: [],
})

onMounted(async () => {
  const { data } = await calibersStore.fetchAll()
  calibers.value = data
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await firearmsStore.create(firearm.value)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
