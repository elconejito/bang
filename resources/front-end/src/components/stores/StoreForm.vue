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
        v-model="store.label"
      />
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        class="form-control"
        id="description"
        name="description"
        v-model="store.description"
      ></textarea>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="form-group">
      <ActionButton text="Add Entry" :is-loading="loading" variant="primary" @click="submit" />
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useGunStoresStore } from '@/stores/gunStores'
import ActionButton from '@/components/ActionButton.vue'
import FormError from '@/components/FormError.vue'

const emit = defineEmits(['complete'])

const gunStoresStore = useGunStoresStore()

const loading = ref(false)
const error = ref(null)
const store = ref({
  label: '',
  description: '',
})

async function submit() {
  error.value = null
  loading.value = true
  try {
    await gunStoresStore.create(store.value)
    emit('complete')
  } catch (err) {
    if (err.response?.data?.errors) err.errorBag = err.response.data.errors
    error.value = err
  } finally {
    loading.value = false
  }
}
</script>
