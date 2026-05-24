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
        placeholder="Label of Caliber"
        required
        v-model="caliber.label"
      />
      <p class="mt-1 text-xs text-gray-500">
        The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
      </p>
    </div>

    <div class="mb-4">
      <label for="short_label" class="block text-sm font-medium text-gray-700 mb-1">
        Short Label <span class="text-red-500">*</span>
      </label>
      <input
        type="text"
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="short_label"
        name="short_label"
        placeholder="Short Label for Caliber"
        required
        v-model="caliber.short_label"
      />
      <p class="mt-1 text-xs text-gray-500">The label used throughout the app</p>
    </div>

    <div class="mb-4">
      <label for="caliber_type_id" class="block text-sm font-medium text-gray-700 mb-1">
        Caliber Type <span class="text-red-500">*</span>
      </label>
      <select
        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        id="caliber_type_id"
        name="caliber_type_id"
        required
        v-model="caliber.caliber_type_id"
      >
        <option v-for="(caliberType, i) in caliberTypes" :value="caliberType.id" :key="i">
          {{ caliberType.label }}
        </option>
      </select>
      <p class="mt-1 text-xs text-gray-500">
        The type of caliber such as rimfire, centerfire, or shotgun
      </p>
    </div>

    <FormError v-if="error" :error="error" />

    <div class="mt-6">
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
